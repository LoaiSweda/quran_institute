<?php

// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // توجيه بناءً على الدور
            switch (Auth::user()->role->name) {
                case 'super admin':
                    return redirect()->intended('/super-admin/dashboard');
                case 'admin':
                    return redirect()->intended('/admin/dashboard');
                case 'institute manager':
                    return redirect()->intended('/manager/dashboard');
                case 'teacher':
                    return redirect()->intended('/teacher/dashboard');
                case 'student':
                    return redirect()->intended('/student/dashboard');
                case 'guardian':
                    return redirect()->intended('/guardian/dashboard');
                default:
                    return redirect()->intended('/');
            }
        }

        return back()
            ->withErrors(['email' => 'بيانات الدخول غير صحيحة'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
