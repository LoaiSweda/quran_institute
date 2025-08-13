<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\GuardianController;
use App\Http\Controllers\Api\EducationClassController;

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])
     ->middleware('auth:sanctum');


Route::middleware(['auth:sanctum','role:student'])
    ->prefix('student')
    ->group(function() {
        Route::get('institute-announcements', [StudentController::class, 'instituteAnnouncements']);
        Route::get('class-announcements/{class}', [StudentController::class, 'classAnnouncements']);
        Route::get('announcements/{ad}', [StudentController::class, 'announcementDetail']);
        
        Route::get('classes', [StudentController::class, 'classes']); 
        Route::get('classes/{class}', [StudentController::class, 'classDetail']);

        Route::get('profile', [StudentController::class, 'profile']);
        Route::get('weekly-schedule', [StudentController::class, 'weeklySchedule']);
    });



    
/*

Route::middleware(['auth:sanctum','role:guardian'])
    ->prefix('guardian')
    ->group(function() {
        Route::get('profile', [GuardianController::class, 'profile']);
        Route::get('children', [GuardianController::class, 'children']);
    });

*/