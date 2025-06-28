<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Teacher\AnnouncementController;
use Illuminate\Support\Facades\Route;

// عرض نموذج الدخول عند "/"
Route::get('/', [LoginController::class, 'showLoginForm'])
     ->name('home')
     ->middleware('guest');

// مسارات الدخول
Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');

// نسيت كلمة المرور
Route::middleware('guest')->group(function () {
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
         ->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
         ->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
         ->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
         ->name('password.update');
});

// تسجيل الخروج
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// مسارات الأدوار
Route::middleware(['auth','role:super admin'])
     ->prefix('super-admin')
     ->group(fn() => Route::view('dashboard','dashboards.super_admin'));

Route::middleware(['auth','role:admin'])
     ->prefix('admin')
     ->group(fn() => Route::view('dashboard','dashboards.admin'));

Route::middleware(['auth','role:institute manager'])
     ->prefix('manager')
     ->group(fn() => Route::view('dashboard','dashboards.manager'));



Route::middleware(['auth','role:teacher'])
     ->prefix('teacher')
     ->name('teacher.')
     ->group(function () {
        // لوحة المعلم
        Route::view('dashboard','dashboards.teacher')->name('dashboard');

        Route::get('announcements', [AnnouncementController::class,'index'])
            ->name('announcements.index');

        // عرض تفاصيل الإعلان
        Route::get('announcements/{ad}', [AnnouncementController::class,'show'])
            ->name('announcements.show');

        // نموذج التعديل
        Route::get('announcements/{ad}/edit', [AnnouncementController::class,'edit'])
            ->name('announcements.edit');

        // حفظ التعديل
        Route::put('announcements/{ad}', [AnnouncementController::class,'update'])
            ->name('announcements.update');

        // حذف الإعلان
        Route::delete('announcements/{ad}', [AnnouncementController::class,'destroy'])
            ->name('announcements.destroy');

        // إضافة إعلان جديد
        Route::post('announcements', [AnnouncementController::class,'store'])
            ->name('announcements.store');
});
