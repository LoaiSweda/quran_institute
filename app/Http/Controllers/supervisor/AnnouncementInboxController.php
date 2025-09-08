<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Models\UserAd;


class AnnouncementInboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    /**
     * يحدّد المعهد الحالي بناءً على المعاهد المرتبطة بالمستخدم + ?institute_id
     */
    protected function currentInstitute(Request $request): Institute
    {
        $user = $request->user();
        $ids  = $user->institutes()->pluck('institutes.id');
        abort_if($ids->isEmpty(), 403, 'لا تملك صلاحية على أي معهد.');

        $picked = (int) $request->query('institute_id', (int) $ids->first());
        abort_if(! $ids->contains($picked), 403, 'هذا المعهد غير مرتبط بحسابك.');

        return Institute::findOrFail($picked);
    }

    /**
     * يبني كويري أساس صندوق الوارد للمشرف (admin) في معهد معيّن
     */
    protected function baseInboxQuery(Institute $inst): Builder
    {
        $today = now()->toDateString();
        $currentUserId = Auth::id();

        return Ad::query()
            // (أ) إعلان يخص نفس المعهد
            // (ب) أو إعلان عام من السوبر أدمن
            ->where(function (Builder $q) use ($inst) {
                $q->where('institute_id', $inst->id)
                  ->orWhere(function (Builder $q2) {
                      $q2->whereNull('institute_id')
                         ->whereHas('publisher.role', function (Builder $r) {
                             $r->where('name', 'super admin');
                         });
                  });
            })
            // موجّه لدور admin
            ->whereHas('userAds', function (Builder $q) {
                $q->where('watches_role', 'admin');
            })
            // ليس أنا الناشر
            ->where('user_id', '<>', $currentUserId)
            // فعّال وغير منتهٍ
            ->where('status', 'active')
            ->whereDate('end_date', '>=', $today);
    }

    /**
     * فهرس الإعلانات: مع تعليم جميع المطابقة كمقروءة فور فتح الصفحة
     */
    public function index(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $base = $this->baseInboxQuery($inst);

        // نجلب الصفحة الحالية مع العلاقات
        $ads = (clone $base)
            ->with(['type', 'publisher.role', 'institute'])
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        // ❗ تعليم كل الإعلانات المطابقة كمقروءة (وليس فقط عناصر الصفحة)
        $idsToMark = (clone $base)->select('ads.id')->pluck('ads.id');
        if ($idsToMark->isNotEmpty()) {
            DB::table('user_ads')
                ->whereIn('ads_id', $idsToMark)
                ->where('watches_role', 'admin')
                ->update([
                    'is_read' => 1,
                    'read_at' => now(),
                ]);
        }

        return view('supervisor.announcements.inbox.index', compact('ads'));
    }

    /**
     * عرض إعلان واحد + تعليم كمقروء
     */
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

        // ❗ تعليم هذا الإعلان كمقروء
        $ad->userAds()
            ->where('watches_role', 'admin')
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        $ad->load(['type','publisher.role','institute','userAds']);

        return view('supervisor.announcements.inbox.show', compact('ad'));
    }

    /**
     * عدد غير المقروء لاستخدامه في شارة السايدبار
     */
    public function unreadCount(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $count = $this->baseInboxQuery($inst)
            ->whereHas('userAds', function (Builder $q) {
                $q->where('watches_role', 'admin')
                  ->where(function (Builder $qq) {
                      $qq->whereNull('is_read')->orWhere('is_read', 0);
                  });
            })
            ->count();

        return response()->json(['count' => $count]);
    }
}
