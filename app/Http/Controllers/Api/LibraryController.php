<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\LibraryResource;
use App\Models\Category;
use App\Models\Guardian;
use App\Models\Library;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{
    /**
     * قائمة الكاتيغوري التي تحتوي عناصر مرئية تنتمي لمعاهد الطالب.
     * يدعم ?q= للبحث باسم الكاتيغوري، ويرجع items_count لكل كاتيغوري.
     */
    public function categories(Request $request)
    {
        $user = $request->user();
        $instituteIds = $this->studentInstituteIds($user);

        if (empty($instituteIds)) {
            return CategoryResource::collection(collect());
        }

        $q = trim((string) $request->query('q'));

        $query = Category::query()
            ->whereHas('libraries', function ($q2) use ($instituteIds) {
                $q2->where('is_visible', true)
                   ->whereIn('institute_id', $instituteIds);
            })
            ->withCount(['libraries as items_count' => function ($q2) use ($instituteIds) {
                $q2->where('is_visible', true)
                   ->whereIn('institute_id', $instituteIds);
            }])
            ->orderBy('name');

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        $categories = $query->get();

        return CategoryResource::collection($categories);
    }

    /**
     * ملفات كاتيغوري معيّن تخص معاهد الطالب فقط ومرئية is_visible=1.
     * يدعم ?q= للبحث بالاسم/المؤلف/الوصف، و ?per_page= للصفحات.
     */
    public function categoryItems(Request $request, Category $category)
    {
        $user = $request->user();
        $instituteIds = $this->studentInstituteIds($user);

        if (empty($instituteIds)) {
            // لا يوجد معهد مرتبط بالطالب
            return response()->json([
                'category' => (new \App\Http\Resources\CategoryResource($category)),
                'data'     => [],
                'meta'     => ['total' => 0],
            ], 200);
        }

        $q = trim((string) $request->query('q'));
        $perPage = (int) $request->integer('per_page', 20);
        $perPage = $perPage > 0 ? min($perPage, 100) : 20;

        $items = Library::query()
            ->where('category_id', $category->id)
            ->where('is_visible', true)
            ->whereIn('institute_id', $instituteIds)
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('author', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%")
                      ->orWhere('isbn', 'like', "%{$q}%");
                });
            })
            ->with(['file']) // نحتاج الملف لنبني رابط التحميل
            ->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'category' => (new \App\Http\Resources\CategoryResource($category)),
            'data'     => LibraryResource::collection($items)->resource, // نبقي الـ paginator كما هو
            'meta'     => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ],
        ], 200);
    }

    /**
     * معاهد الطالب (أو ولي الأمر: معاهد أبنائه) بناءً على pivot institute_user.
     */
    private function studentInstituteIds(User $user): array
    {
        // لو هو طالب
        if ($user->hasRole('student')) {
            return $user->institutes()
                ->wherePivot('role_institute', 'student')
                ->pluck('institutes.id')
                ->toArray();
        }

        // لو هو ولي أمر: استخرج أبناءه ثم معاهد الأبناء
        if ($user->hasRole('guardian')) {
            $guardianId = DB::table('guardians')->where('user_id', $user->id)->value('id');
            if (!$guardianId) return [];

            $childUserIds = DB::table('students')
                ->where('guardian_id', $guardianId)
                ->pluck('user_id');

            if ($childUserIds->isEmpty()) return [];

            return DB::table('institute_user')
                ->whereIn('user_id', $childUserIds)
                ->where('role_institute', 'student')
                ->pluck('institute_id')
                ->unique()
                ->values()
                ->toArray();
        }

        // أدوار أخرى غير مسموحة هنا
        return [];
    }
}
