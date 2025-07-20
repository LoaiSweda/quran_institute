<?php
// app/Http/Controllers/Api/AuthController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
            'type'     => 'required|in:0,1',
        ]);

        if ($data['type'] === '0') {
            // تسجيل دخول طالب
            $student = \App\Models\Student::with(['classes', 'exams', 'progress']) // لو حابب تجيب العلاقات
                ->where('phone', $data['phone'])
                ->first();

            if (! $student) {
                return response()->json(['message' => 'رقم الهاتف غير مسجل كطالب'], 401);
            }

            $user = $student->user;
        } else {
            // تسجيل دخول ولي أمر
            $guardian = \App\Models\Guardian::with('students')
                ->where('phone', $data['phone'])
                ->first();

            if (! $guardian) {
                return response()->json(['message' => 'رقم الهاتف غير مسجل كولي أمر'], 401);
            }

            $user = $guardian->user;
        }

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        // بناء الـ JSON للرد بناءً على النوع
        if ($data['type'] === '0') {
            return response()->json([
                'type'    => 'student',
                'token'   => $token,
                'student' => [
                    'id'                => $student->id,
                    'first_name'        => $student->first_name,
                    'last_name'         => $student->last_name,
                    'phone'             => $student->phone,
                    'address'           => $student->address,
                    'birthdate'         => $student->birthdate->format('Y-m-d'),
                    'father_name'       => $student->father_name,
                    'points'            => $student->points,
                    'present_percentage'=> $student->present_percentage,
                    // إذا حابب ترسل العلاقات:
                    'classes' => $student->classes,
                    'exams'   => $student->exams,
                    'progress'=> $student->progress,
                ],
            ]);
        } else {
            return response()->json([
                'type'     => 'guardian',
                'token'    => $token,
                'guardian' => [
                    'id'       => $guardian->id,
                    'firstname'=> $guardian->firstname,
                    'lastname' => $guardian->lastname,
                    'phone'    => $guardian->phone,
                    'address'  => $guardian->address,
                    'students' => $guardian->students->map(function($s){
                        return [
                            'id'         => $s->id,
                            'first_name' => $s->first_name,
                            'last_name'  => $s->last_name,
                            'phone'      => $s->phone,
                        ];
                    }),
                ],
            ]);
        }
    }

    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}
