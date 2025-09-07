<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\GuardianController;
use App\Http\Controllers\Api\EducationClassController;
use App\Http\Controllers\Api\GuardianStudentsController;
use App\Http\Controllers\Api\GuardianImpersonationController;
use App\Http\Controllers\Api\LibraryController;


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])
     ->middleware('auth:sanctum');


Route::middleware(['auth:sanctum','role:student,guardian'])
    ->prefix('student')
    ->group(function() {
        Route::get('institute-announcements', [StudentController::class, 'instituteAnnouncements']);
        Route::get('class-announcements/{class}', [StudentController::class, 'classAnnouncements']);
        Route::get('announcements/{ad}', [StudentController::class, 'announcementDetail']);
        
        Route::get('classes', [StudentController::class, 'classes']); 
        Route::get('classes/{class}', [StudentController::class, 'classDetail']);

        Route::get('profile', [StudentController::class, 'profile']);
        Route::get('weekly-schedule', [StudentController::class, 'weeklySchedule']);

        Route::get('library/categories', [LibraryController::class, 'categories']);
        Route::get('library/categories/{category}', [LibraryController::class, 'categoryItems']);

    });



    

Route::middleware(['auth:sanctum','role:guardian'])
    ->prefix('guardians')
    ->group(function() {
        Route::get('{guardian}/students', [GuardianStudentsController::class, 'index']);
        Route::post('students/{student}/impersonate', [GuardianImpersonationController::class, 'impersonate']);
        // (اختياري) لإلغاء كل توكنات الانتحال لطالب معيّن
        Route::delete('students/{student}/impersonate', [GuardianImpersonationController::class, 'revokeForStudent']);

    });
