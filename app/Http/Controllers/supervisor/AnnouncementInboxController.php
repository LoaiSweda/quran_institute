<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementInboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    protected function currentInstitute(Request $request): Institute
    {
        $user = $request->user();
        $ids  = $user->institutes()->pluck('institutes.id');
        abort_if($ids->isEmpty(), 403, 'لا تملك صلاحية على أي معهد.');

        $picked = (int) $request->query('institute_id', (int) $ids->first());
        abort_if(! $ids->contains($picked), 403, 'هذا المعهد غير مرتبط بحسابك.');

        return Institute::findOrFail($picked);
    }

    public function index(Request $request)
    {
        $inst = $this->currentInstitute($request);

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
            ->whereHas('userAds', fn($q)=> $q->where('watches_role','admin'))
            ->where('user_id', '<>', Auth::id())
            ->where('status','active')
            ->whereDate('end_date','>=', now()->toDateString())
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('supervisor.announcements.inbox.index', compact('ads'));
    }

    public function show(Request $request, Ad $ad)
    {
        $inst = $this->currentInstitute($request);

        $isFromSameInstitute = ((int)$ad->institute_id === (int)$inst->id);
        $isGlobalFromSuperAdmin =
            is_null($ad->institute_id) &&
            optional($ad->publisher?->role)->name === 'super admin';

        $allowed =
            ($isFromSameInstitute || $isGlobalFromSuperAdmin) &&
            $ad->status === 'active' &&
            now()->toDateString() <= (string)$ad->end_date &&
            $ad->user_id !== Auth::id() &&
            $ad->userAds()->where('watches_role','admin')->exists();

        abort_unless($allowed, 403);

        $ad->load(['type','publisher.role','institute','userAds']);
        return view('supervisor.announcements.inbox.show', compact('ad'));
    }
}
