<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Institute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserAd;


class AnnouncementInboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:institute manager']);
    }

    /**
     * المعهد الحالي الخاص بمدير المعهد المسجّل
     */
    protected function currentInstitute(): Institute
    {
        return Institute::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * يبني كويري الإعلانات المطابقة لصندوق واردي (مدير معهد)
     */
    protected function baseInboxQuery(Institute $inst): Builder
    {
        $today = now()->toDateString();
        $currentUserId = Auth::id();

        return Ad::query()
            // (أ) إعلان من نفس المعهد
            // (ب) أو إعلان عام من السوبر أدمن (institute_id NULL والناشر دوره super admin)
            ->where(function (Builder $q) use ($inst) {
                $q->where('institute_id', $inst->id)
                  ->orWhere(function (Builder $q2) {
                      $q2->whereNull('institute_id')
                         ->whereHas('publisher.role', function (Builder $r) {
                             $r->where('name', 'super admin');
                         });
                  });
            })
            // موجّه لدور مدير المعهد
            ->whereHas('userAds', function (Builder $q) {
                $q->where('watches_role', 'manager');
            })
            // ليس أنا الناشر
            ->where('user_id', '<>', $currentUserId)
            // فعّال وغير منتهٍ
            ->where('status', 'active')
            ->whereDate('end_date', '>=', $today);
    }

    /**
     * فهرس الإعلانات (يتم هنا تعليم الكل كمقروء فور الدخول)
     */
    public function index(Request $request)
    {
        $inst = $this->currentInstitute();

        $base = $this->baseInboxQuery($inst);

        // نجلب الصفحة الحالية مع العلاقات للعرض
        $ads = (clone $base)
            ->with(['type', 'publisher.role', 'institute'])
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        // ❗ تعليم "كل" الإعلانات المطابقة كمقروءة فور فتح الصفحة (وليس فقط عناصر الصفحة الحالية)
        $idsToMark = (clone $base)->select('ads.id')->pluck('ads.id');
        if ($idsToMark->isNotEmpty()) {
            DB::table('user_ads')
                ->whereIn('ads_id', $idsToMark)
                ->where('watches_role', 'manager')
                ->update([
                    'is_read' => 1,
                    'read_at' => now(),
                ]);
        }

        return view('manager.announcements.inbox.index', compact('ads'));
    }

    /**
     * عرض إعلان واحد (نعلّمه كمقروء أيضًا)
     */
    public function show(Request $request, Ad $ad)
    {
        $inst = $this->currentInstitute();

        $isFromSameInstitute = ((int) $ad->institute_id === (int) $inst->id);
        $isGlobalFromSuperAdmin =
            is_null($ad->institute_id) &&
            optional($ad->publisher?->role)->name === 'super admin';

        $allowed =
            ($isFromSameInstitute || $isGlobalFromSuperAdmin) &&
            $ad->status === 'active' &&
            now()->toDateString() <= (string) $ad->end_date &&
            $ad->user_id !== Auth::id() &&
            $ad->userAds()->where('watches_role', 'manager')->exists();

        abort_unless($allowed, 403);

        // ❗ تعليم هذا الإعلان كمقروء
        $ad->userAds()
            ->where('watches_role', 'manager')
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        $ad->load(['type', 'publisher.role', 'institute', 'userAds']);

        return view('manager.announcements.inbox.show', compact('ad'));
    }

    /**
     * إرجاع عدد غير المقروء لصندوق الوارد (لاستخدامه في شارة السايدبار)
     */
    public function unreadCount(Request $request)
    {
        $inst = $this->currentInstitute();

        $count = $this->baseInboxQuery($inst)
            ->whereHas('userAds', function (Builder $q) {
                $q->where('watches_role', 'manager')
                  ->where(function (Builder $qq) {
                      $qq->whereNull('is_read')->orWhere('is_read', 0);
                  });
            })
            ->count();

        return response()->json(['count' => $count]);
    }
}
