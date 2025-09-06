<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementInboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:institute manager']);
    }

    protected function currentInstitute(): Institute
    {
        return Institute::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $inst = $this->currentInstitute();

        $ads = Ad::with(['type','publisher.role','institute'])
            ->where(function($q) use ($inst) {
                $q->where('institute_id', $inst->id)
                    ->orWhere(function($q2){
                        $q2->whereNull('institute_id')
                            ->whereHas('publisher.role', function($r){
                                $r->where('name', 'super admin');
                            });
                    });
            })
            ->whereHas('userAds', fn($q)=> $q->where('watches_role','manager'))
            ->where('user_id', '<>', Auth::id())
            ->where('status','active')
            ->whereDate('end_date','>=', now()->toDateString())
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('manager.announcements.inbox.index', compact('ads'));
    }

    public function show(Request $request, Ad $ad)
    {
        $inst = $this->currentInstitute();

        $isFromSameInstitute = ((int)$ad->institute_id === (int)$inst->id);
        $isGlobalFromSuperAdmin =
            is_null($ad->institute_id) &&
            optional($ad->publisher?->role)->name === 'super admin';

        $allowed =
            ($isFromSameInstitute || $isGlobalFromSuperAdmin) &&
            $ad->status === 'active' &&
            now()->toDateString() <= (string)$ad->end_date &&
            $ad->user_id !== Auth::id() &&
            $ad->userAds()->where('watches_role','manager')->exists();

        abort_unless($allowed, 403);

        $ad->load(['type','publisher.role','institute','userAds']);
        return view('manager.announcements.inbox.show', compact('ad'));
    }
}
