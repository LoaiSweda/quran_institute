<?php
// app/Http/Controllers/Api/AuthController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // تسجيل الدخول وإصدار توكن
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($data)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id','email','role_id']),
            'token' => $token,
        ]);
    }

    // تسجيل الخروج (إبطال كل التوكنات)
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }
}
