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
            $student = \App\Models\Student::where('phone', $data['phone'])->first();
            if (! $student) {
                return response()->json(['message' => 'رقم الهاتف غير مسجل كطالب'], 401);
            }
            $user = $student->user;
        } else {
            $guardian = \App\Models\Guardian::where('phone', $data['phone'])->first();
            if (! $guardian) {
                return response()->json(['message' => 'رقم الهاتف غير مسجل كولي أمر'], 401);
            }
            $user = $guardian->user;
        }

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;
        return response()->json([
            'user'  => $user->only(['id','email','role_id']),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}
