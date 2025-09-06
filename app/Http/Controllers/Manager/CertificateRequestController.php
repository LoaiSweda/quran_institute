<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreCertificateRequestRequest;
use App\Models\CertificateRequest;
use App\Models\EducationClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use niklasravnsborg\LaravelPdf\Facades\Pdf as PDF; // <- wrapper alias

class CertificateRequestController extends Controller
{
    public function index(Request $request){
        $manager = $request->user();
        $institute = $manager->institute;
        abort_unless($institute, 403, 'لا يوجد معهد مرتبط بالمستخدم الحالي.');


        // Subjects owned by this institute
        $subjects = Subject::where('institute_id', $institute->id)
        ->orderBy('name')
        ->get();


        $subjectId = $request->integer('subject_id');
        $selectedSubject = $subjectId ? $subjects->firstWhere('id', $subjectId) : null;


        $eligibleStudents = collect();
        if ($selectedSubject && $selectedSubject->is_finished) {
            // Students enrolled in any class of the selected subject
            $classIds = EducationClass::where('subject_id', $selectedSubject->id)->pluck('id');
            if ($classIds->isNotEmpty()) {
                $userIds = DB::table('users_classes')->whereIn('class_id', $classIds)->pluck('user_id');
                if ($userIds->isNotEmpty()) {
                    $eligibleStudents = Student::with('user')
                    ->whereIn('user_id', $userIds)
                    ->orderBy('last_name')
                    ->get();
                }
            }
        }


        // Existing requests for this institute (with optional subject filter)
        $requestsQuery = CertificateRequest::with(['student.user', 'subject'])
            ->forInstitute($institute->id)
            ->orderByDesc('created_at');


        if ($selectedSubject) {
            $requestsQuery->where('subject_id', $selectedSubject->id);
        }


        $requests = $requestsQuery->paginate(12)->withQueryString();


        return view('manager.certificates.index', compact(
            'subjects', 'selectedSubject', 'eligibleStudents', 'requests'
        ));
    }

    public function store(StoreCertificateRequestRequest $request)
    {
        $manager = $request->user();
        $instituteId = optional($manager->institute)->id;
        abort_unless($instituteId, 403, 'لا يوجد معهد مرتبط بالمستخدم الحالي.');

        // Subject must belong to this institute
        $subject = Subject::where('institute_id', $instituteId)
            ->findOrFail($request->integer('subject_id'));

        // Finished subject rule: end_date in past OR is_active == false
        $isFinished = false;
        if ($subject->end_date) {
            $isFinished = Carbon::parse($subject->end_date)->isPast();
        }
        if (! $isFinished && isset($subject->is_active)) {
            $isFinished = ! (bool) $subject->is_active;
        }
        if (! $isFinished) {
            return back()
                ->withErrors(['subject_id' => 'لا يمكن طلب الشهادة إلا بعد انتهاء المادة.'])
                ->withInput();
        }

        // Verify the student is enrolled in any class of this subject
        $classIds = EducationClass::where('subject_id', $subject->id)->pluck('id');
        if ($classIds->isEmpty()) {
            return back()->withErrors(['student_id' => 'لا توجد حلقات لهذه المادة.'])->withInput();
        }

        $studentUserId = Student::where('id', $request->integer('student_id'))->value('user_id');
        if ($studentUserId) {
            $enrolled = DB::table('users_classes')
                ->whereIn('class_id', $classIds)
                ->where('user_id', $studentUserId)
                ->exists();
        } else {
            $enrolled = false;
        }

        if (! $enrolled) {
            return back()->withErrors(['student_id' => 'الطالب غير مسجّل في حلقات هذه المادة.'])->withInput();
        }

        // Create once per (student, subject). Assumes you added a unique index or cleaned duplicates.
        $model = CertificateRequest::firstOrCreate(
            ['student_id' => $request->integer('student_id'), 'subject_id' => $subject->id],
            ['status' => CertificateRequest::STATUS_PENDING, 'request_at' => now()]
        );

        return back()->with('success',
            $model->wasRecentlyCreated
                ? 'تم إرسال طلب الشهادة إلى المشرف العام.'
                : 'يوجد طلب سابق قيد المعالجة أو مكتمل لهذا الطالب.'
        );
    }

    public function export(Request $request, CertificateRequest $certificateRequest)
    {
        if ($certificateRequest->file_id && class_exists(\App\Models\File::class)) {
            $file = \App\Models\File::find($certificateRequest->file_id);
            if ($file && Storage::disk('public')->exists($file->path)) {
                return response()->download(Storage::disk('public')->path($file->path));
            }
        }
        $admin = optional(Auth::user())->admin;   // ✅ null-safe
        $manager   = $request->user();
        $institute = $manager->institute;
        abort_unless($institute, 403);

        // Scope check
        abort_unless(optional($certificateRequest->subject)->institute_id === $institute->id, 403);

        // Only after approval
        if ($certificateRequest->status !== CertificateRequest::STATUS_APPROVED) {
            return back()->withErrors(['export' => 'التصدير متاح بعد الموافقة فقط.']);
        }

        $student = $certificateRequest->student;
        $subject = $certificateRequest->subject;

        // Data passed to the template
        // helper to turn a public-disk path into data URI
        $toDataUri = function (?string $diskPath): ?string {
            if (!$diskPath) return null;
            $abs = \Storage::disk('public')->path($diskPath);
            if (!is_file($abs)) return null;
            $mime = @mime_content_type($abs) ?: 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($abs));
        };

        // build sources
        $logoSrc            = $toDataUri($institute->image ?? null);                       // شعار المعهد (إن وجد)
        $adminStampSrc      = $toDataUri($admin->institution_stamp_path ?? null);         // ختم المؤسسة (من Admin)
        $instStampSrc       = $toDataUri($institute->institute_stamp_path ?? null);        // ختم المعهد
        $directorSignSrc    = $toDataUri($institute->director_signature_path ?? null);     // توقيع مدير المعهد
        $teacherSignSrc = $toDataUri($teacher->signature_path ?? null);

        $issuedAt = now()->locale('ar');

        $viewData = [
            'institute'            => $institute,
            'student'              => $certificateRequest->student,
            'subject'              => $certificateRequest->subject,
            'certificate'          => $certificateRequest,
            'issued_at'            => $issuedAt,
            // image data-uris:
            'logo_src'             => $logoSrc,
            'admin_stamp_src'      => $adminStampSrc,
            'institute_stamp_src'  => $instStampSrc,
            'director_signature_src'=> $directorSignSrc,
            'teacher_signature_src'=> $teacherSignSrc ?? null,
        ];

        // Render PDF (A4 landscape)
        $pdf = PDF::loadView('pdf.certificates.certificate', $viewData);

        // Persist to storage/public/certificates/...
        $filename = sprintf(
            'certificate-%d-st%s-sub%s.pdf',
            $certificateRequest->id,
            $student?->id ?? 'x',
            $subject?->id ?? 'x'
        );
        $path = "certificates/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        // (Optional) Save/attach file record if you have a File model
        if (class_exists(\App\Models\File::class)) {
            try {
                $file = \App\Models\File::create([
                    'name'    => $filename,
                    'path'    => $path,
                    'mime'    => 'application/pdf',
                    'size'    => Storage::disk('public')->size($path),
                ]);
                $certificateRequest->update(['file_id' => $file->id]);
            } catch (\Throwable $e) {
                // ignore if schema differs; file is still saved on disk
            }
        }

        // Return the download
        return response()->download(Storage::disk('public')->path($path));
    }
}
