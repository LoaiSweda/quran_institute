<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;

class AnnouncementInboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:teacher']);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $instituteIds = $user->institutes()->pluck('institutes.id');

        $ads = Ad::with(['type','publisher.role','institute'])
            // نطاق المعهد أو إعلان عام من سوبر أدمن فقط
            ->where(function($q) use ($instituteIds) {
                $q->whereIn('institute_id', $instituteIds)
                    ->orWhere(function($q2){
                        $q2->whereNull('institute_id')
                            ->whereHas('publisher.role', function($r){
                                $r->where('name', 'super admin');
                            });
                    });
            })
            // موجّه للمعلم
            ->whereHas('userAds', fn($q)=> $q->where('watches_role','teacher'))
            // نشط وغير منتهٍ
            ->where('status','active')
            ->whereDate('end_date','>=', now()->toDateString())
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('teacher.announcements.inbox.index', compact('ads'));
    }

    public function show(Request $request, Ad $ad)
    {
        $user = $request->user();
        $instituteIds = $user->institutes()->pluck('institutes.id');

        $isFromSameInstitute = $instituteIds->contains((int)$ad->institute_id);

        $isGlobalFromSuperAdmin =
            is_null($ad->institute_id)
            && optional($ad->publisher?->role)->name === 'super admin';

        $allowed =
            ($isFromSameInstitute || $isGlobalFromSuperAdmin)
            && $ad->status === 'active'
            && now()->toDateString() <= (string)$ad->end_date
            && $ad->userAds()->where('watches_role','teacher')->exists();

        abort_unless($allowed, 403);

        $ad->load(['type','publisher.role','institute','userAds']);
        return view('teacher.announcements.inbox.show', compact('ad'));
    }
}

