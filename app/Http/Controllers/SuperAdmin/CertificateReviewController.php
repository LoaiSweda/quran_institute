<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificateReviewController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $status      = $request->string('status')->toString();        // pending|approved|refused
        $subjectId   = $request->integer('subject_id');
        $instituteId = $request->integer('institute_id');
        $q           = trim((string) $request->get('q'));

        $requests = CertificateRequest::with(['student.user', 'subject'])
            ->when($status, fn($q2) => $q2->where('status', $status))
            ->when($subjectId, fn($q2) => $q2->where('subject_id', $subjectId))
            ->when($instituteId, fn($q2) => $q2->whereHas('subject', fn($s) => $s->where('institute_id', $instituteId)))
            ->when($q, function ($q2) use ($q) {
                $q2->whereHas('student', function ($qs) use ($q) {
                    $qs->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%");
                })->orWhereHas('student.user', function ($qu) use ($q) {
                    $qu->where('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        // For filter dropdowns
        $subjects = Subject::orderBy('name')->get();

        // Tab counters
        $counts = CertificateRequest::selectRaw("status, COUNT(*) c")
            ->groupBy('status')->pluck('c','status');

        return view('super-admin.certificates.index', compact('requests','subjects','counts','status','subjectId','instituteId','q'));
    }

    public function approve(Request $request, CertificateRequest $certificateRequest)
    {
        if ($certificateRequest->status === CertificateRequest::STATUS_APPROVED) {
            return back()->with('info', 'هذا الطلب مُعتمد مسبقًا.');
        }
        if ($certificateRequest->status === CertificateRequest::STATUS_REFUSED) {
            return back()->with('warning', 'لا يمكن اعتماد طلب تم رفضه مسبقًا.');
        }

        DB::transaction(function () use ($certificateRequest, $request) {
            $certificateRequest->update([
                'status'      => CertificateRequest::STATUS_APPROVED,
                'reviewed_at' => now(),
                'user_id'     => $request->user()->id, // المراجع (سوبر أدمن)
            ]);
        });

        return back()->with('success', 'تم اعتماد الطلب بنجاح.');
    }

    public function refuse(Request $request, CertificateRequest $certificateRequest)
    {
        if ($certificateRequest->status === CertificateRequest::STATUS_REFUSED) {
            return back()->with('info', 'هذا الطلب مرفوض مسبقًا.');
        }
        if ($certificateRequest->status === CertificateRequest::STATUS_APPROVED) {
            return back()->with('warning', 'لا يمكن رفض طلب مُعتمد.');
        }

        DB::transaction(function () use ($certificateRequest, $request) {
            $certificateRequest->update([
                'status'      => CertificateRequest::STATUS_REFUSED,
                'reviewed_at' => now(),
                'user_id'     => $request->user()->id,
            ]);
        });

        return back()->with('success', 'تم رفض الطلب.');
    }
}
