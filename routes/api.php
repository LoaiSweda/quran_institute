<?php

// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\GuardianController;

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])
     ->middleware('auth:sanctum');

// مسارات الطلاب
Route::middleware(['auth:sanctum','role:student'])
    ->prefix('student')
    ->group(function() {
        Route::get('profile', [StudentController::class, 'profile']);
        Route::get('classes', [StudentController::class, 'classes']);
    });

// مسارات الأوصياء
Route::middleware(['auth:sanctum','role:guardian'])
    ->prefix('guardian')
    ->group(function() {
        Route::get('profile', [GuardianController::class, 'profile']);
        Route::get('children', [GuardianController::class, 'children']);
    });
