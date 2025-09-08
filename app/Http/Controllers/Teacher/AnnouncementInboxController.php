<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\UserAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementInboxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:teacher']);
    }

    /**
     * يبني كويري أساس صندوق الوارد للمعلّم حسب معاهد المستخدم
     */
    protected function baseInboxQuery(Request $request): Builder
    {
        $user        = $request->user();
        $instituteIds = $user->institutes()->pluck('institutes.id');
        abort_if($instituteIds->isEmpty(), 403, 'لا تملك صلاحية على أي معهد.');

        $today        = now()->toDateString();
        $currentUserId = Auth::id();

        return Ad::query()
            // (أ) إعلان يخص أيّ من معاهد المعلّم
            // (ب) أو إعلان عام من السوبر أدمن
            ->where(function (Builder $q) use ($instituteIds) {
                $q->whereIn('institute_id', $instituteIds)
                  ->orWhere(function (Builder $q2) {
                      $q2->whereNull('institute_id')
                         ->whereHas('publisher.role', function (Builder $r) {
                             $r->where('name', 'super admin');
                         });
                  });
            })
            // موجّه لدور teacher
            ->whereHas('userAds', function (Builder $q) {
                $q->where('watches_role', 'teacher');
            })
            // ليس أنا الناشر (حتى لا تظهر "إعلاناتي" هنا)
            ->where('user_id', '<>', $currentUserId)
            // فعّال وغير منتهٍ
            ->where('status', 'active')
            ->whereDate('end_date', '>=', $today);
    }

    /**
     * فهرس الإعلانات مع تعليم جميع المطابقة كمقروءة فور فتح الصفحة
     */
    public function index(Request $request)
    {
        $base = $this->baseInboxQuery($request);

        // نجلب الصفحة الحالية مع العلاقات
        $ads = (clone $base)
            ->with(['type', 'publisher.role', 'institute'])
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        // ❗ تعليم كل الإعلانات المطابقة في هذا الصندوق كمقروءة
        $idsToMark = (clone $base)->select('ads.id')->pluck('ads.id');
        if ($idsToMark->isNotEmpty()) {
            DB::table('user_ads')
                ->whereIn('ads_id', $idsToMark)
                ->where('watches_role', 'teacher')
                ->update([
                    'is_read' => 1,
                    'read_at' => now(),
                ]);
        }

        return view('teacher.announcements.inbox.index', compact('ads'));
    }

    /**
     * عرض إعلان واحد + تعليم كمقروء
     */
    public function show(Request $request, Ad $ad)
    {
        $user         = $request->user();
        $instituteIds = $user->institutes()->pluck('institutes.id');
        abort_if($instituteIds->isEmpty(), 403, 'لا تملك صلاحية على أي معهد.');

        $isFromSameInstitute = $instituteIds->contains((int) $ad->institute_id);
        $isGlobalFromSuperAdmin =
            is_null($ad->institute_id) &&
            optional($ad->publisher?->role)->name === 'super admin';

        $allowed =
            ($isFromSameInstitute || $isGlobalFromSuperAdmin) &&
            $ad->status === 'active' &&
            now()->toDateString() <= (string) $ad->end_date &&
            $ad->user_id !== Auth::id() &&
            $ad->userAds()->where('watches_role','teacher')->exists();

        abort_unless($allowed, 403);

        // ❗ تعليم هذا الإعلان كمقروء
        $ad->userAds()
            ->where('watches_role', 'teacher')
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        $ad->load(['type','publisher.role','institute','userAds']);

        return view('teacher.announcements.inbox.show', compact('ad'));
    }

    /**
     * عدد غير المقروء لاستخدامه في شارة السايدبار بجوار "إعلانات موجهة لي"
     */
    public function unreadCount(Request $request)
    {
        $count = $this->baseInboxQuery($request)
            ->whereHas('userAds', function (Builder $q) {
                $q->where('watches_role', 'teacher')
                  ->where(function (Builder $qq) {
                      $qq->whereNull('is_read')->orWhere('is_read', 0);
                  });
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    
}
