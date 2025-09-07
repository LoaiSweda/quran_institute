<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guardian;
use App\Models\Student;

class GuardianImpersonationController extends Controller
{
    /**
     * يصدر توكن جديد لمستخدم الطالب بعد التحقق أن الطالب يتبع للولي الحالي.
     * يعيد البيانات الأساسية للطالب + التوكن ليستخدمه العميل بدل توكن الولي.
     */
    public function impersonate(Request $request, Student $student)
    {
        $authUser = $request->user(); // هذا الولي
        $guardian = Guardian::where('user_id', $authUser->id)->first();

        if (! $guardian || (int)$student->guardian_id !== (int)$guardian->id) {
            return response()->json(['message' => 'هذا الطالب لا يتبع لهذا الولي.'], 403);
        }

        $studentUser = $student->user;
        if (! $studentUser) {
            return response()->json(['message' => 'لا يوجد حساب مستخدم مرتبط بهذا الطالب.'], 409);
        }

        // أنشئ توكن باسم واضح (ولك حرية إضافة abilities إن رغبت)
        $token = $studentUser->createToken('impersonated-by:'.$authUser->id, ['impersonated'])->plainTextToken;

        return response()->json([
            'message' => 'تم التبديل إلى جلسة الطالب بنجاح.',
            'type'    => 'student',
            'token'   => $token,
            'student' => [
                'id'         => $student->id,
                'user_id'    => $studentUser->id,
                'first_name' => $student->first_name,
                'last_name'  => $student->last_name,
                'phone'      => $student->phone,
            ],
            // (اختياري) نعيد معرف الولي المُنتحل للشفافية في الواجهة
            'impersonated_by' => [
                'guardian_user_id' => $authUser->id,
            ],
        ], 200);
    }

    /**
     * (اختياري) إلغاء كل توكنات الانتحال الصادرة لهذا الطالب من هذا الولي.
     * عمليًا العميل يكفيه التوقف عن استخدام التوكن، لكن هذا endpoint يفيد للتنظيف.
     */
    public function revokeForStudent(Request $request, Student $student)
    {
        $authUser = $request->user();
        $guardian = Guardian::where('user_id', $authUser->id)->first();

        if (! $guardian || (int)$student->guardian_id !== (int)$guardian->id) {
            return response()->json(['message' => 'هذا الطالب لا يتبع لهذا الولي.'], 403);
        }

        $studentUser = $student->user;
        if (! $studentUser) {
            return response()->json(['message' => 'لا يوجد حساب مستخدم مرتبط بهذا الطالب.'], 409);
        }

        // احذف التوكنات التي أنشأناها باسم يحتوي على 'impersonated-by:<guardianId>'
        $studentUser->tokens()
            ->where('name', 'like', 'impersonated-by:'.$authUser->id.'%')
            ->delete();

        return response()->json(['message' => 'تم إلغاء توكنات الانتحال لهذا الطالب.'], 200);
    }
}
