<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;

class GuardianStudentsController extends Controller
{
    /**
     * إرجاع طلاب وليّ أمر محدد بالـ {guardian} (Route Model Binding).
     * يدعم: ?q= للبحث بالاسم/الهاتف، ?per_page=، ?include=classes,user,guardian
     */
    public function index(Guardian $guardian, Request $request)
    {
        $this->authorizeView($request->user(), $guardian);

        $query = $guardian->students()->newQuery();

        // بحث نصي بسيط
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // علاقات اختيارية عبر include
        $allowedIncludes = ['user', 'guardian', 'classes'];
        $includes = array_filter(explode(',', (string) $request->query('include')));
        $includes = array_values(array_intersect($includes, $allowedIncludes));
        if (!empty($includes)) {
            $query->with($includes);
        }

        // ترتيب افتراضي
        $query->orderBy('first_name')->orderBy('last_name');

        // Pagination
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = $perPage > 0 ? min($perPage, 100) : 15;

        $students = $query->paginate($perPage)->appends($request->query());

        return StudentResource::collection($students);
    }

    /**
     * طلاب وليّ الأمر الحالي (حسب التوكن)
     */
    public function mine(Request $request)
    {
        $user = $request->user();

        // نتوقع أن يكون هذا المستخدم وليّ أمر وله سجل Guardian
        $guardian = Guardian::where('user_id', $user->id)->firstOrFail();

        // نعيد استخدام index logic
        return $this->index($guardian, $request);
    }

    /**
     * تفويض بسيط: يسمح للمدير/الأدمن، أو نفس وليّ الأمر فقط.
     */
    protected function authorizeView($authUser, Guardian $guardian): void
    {
        if (!$authUser) {
            abort(401);
        }

        // أدوارك لديك عبر hasRole على User
        if ($authUser->hasRole(['institute manager', 'admin'])) {
            return;
        }

        // لو كان وليّ أمر: لا يرى إلا نفسه
        if ($authUser->hasRole('guardian') && (int)$authUser->id === (int)$guardian->user_id) {
            return;
        }

        // بإمكانك إضافة أدوار أخرى مثل "teacher" إن لزم
        abort(403, 'غير مسموح بعرض طلاب هذا الولي.');
    }
}
