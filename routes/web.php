<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Manager\GuardiansController;
use App\Http\Controllers\Manager\SessionScheduleController;
use App\Http\Controllers\Manager\StudentsController;
use App\Http\Controllers\Manager\TeachersController;
use App\Http\Controllers\Teacher\AnnouncementController;
use App\Http\Controllers\Teacher\ScheduleController;
use App\Http\Controllers\Teacher\StudentController;
use App\Http\Controllers\Teacher\ExamController;
use App\Http\Controllers\Teacher\ClassController;
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

// routes/web.php
use App\Http\Controllers\Manager\ClassesController;
use App\Http\Controllers\Manager\ClasStudentsController;
use App\Http\Controllers\Manager\SubjectsController;

Route::middleware(['auth', 'role:institute manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        // لوحة التحكم
        Route::view('dashboard', 'dashboards.manager')->name('dashboard');

        // إدارة الحلقات (Classes = الحلقات)
        Route::prefix('classes')->name('classes.')->group(function(){

            // 1) CRUD للحلقات
            Route::get('/', [ClassesController::class,'index'])->name('index');
            Route::get('create', [ClassesController::class,'create'])->name('create');
            Route::post('/', [ClassesController::class,'store'])->name('store');

            // 2) **هنا** مسارات إدارة طلاب الحلقة
            Route::get('{class}/students',          [ClasStudentsController::class,'index'])  ->name('students.index');
            Route::get('{class}/students/create',   [ClasStudentsController::class,'create'])->name('students.create');
            Route::post('{class}/students',         [ClasStudentsController::class,'store']) ->name('students.store');
            Route::delete('{class}/students/{user}',[ClasStudentsController::class,'destroy'])->name('students.destroy');

            // 3) ثم مسار العرض العام للحلقة
            Route::get('{class}',   [ClassesController::class,'show'])->name('show');
            Route::get('{class}/edit',[ClassesController::class,'edit'])->name('edit');
            Route::put('{class}',   [ClassesController::class,'update'])->name('update');
            Route::delete('{class}',[ClassesController::class,'destroy'])->name('destroy');

            Route::prefix('{class}/schedules')
                ->name('schedules.')
                ->group(function(){
                    Route::get('create',   [SessionScheduleController::class,'create'])
                        ->name('create');
                    Route::post('/',       [SessionScheduleController::class,'store'])
                        ->name('store');
                    Route::get('{schedule}/edit', [SessionScheduleController::class,'edit'])
                        ->name('edit');
                    Route::put('{schedule}',      [SessionScheduleController::class,'update'])
                        ->name('update');
                    Route::delete('{schedule}',   [SessionScheduleController::class,'destroy'])
                        ->name('destroy');
                });
        });


        // إدارة أولياء الأمور
        Route::prefix('guardians')->name('guardians.')->group(function () {
            // 4.4.6.3 عرض جميع أولياء الأمور
            Route::get('/', [GuardiansController::class, 'index'])->name('index');

            // 4.4.6.1 إنشاء ولي أمر (عرض النموذج + حفظ)
            Route::get('create', [GuardiansController::class, 'create'])->name('create');
            Route::post('/', [GuardiansController::class, 'store'])->name('store');

            // 4.4.6.4 عرض ولي أمر محدد
            Route::get('{guardian}', [GuardiansController::class, 'show'])->name('show');

            // 4.4.6.1 تعديل ولي أمر (عرض النموذج + حفظ)
            Route::get('{guardian}/edit', [GuardiansController::class, 'edit'])->name('edit');
            Route::put('{guardian}', [GuardiansController::class, 'update'])->name('update');

            // 4.4.6.2 حذف/تعطيل ولي أمر
            Route::delete('{guardian}', [GuardiansController::class, 'destroy'])->name('destroy');
        });



        // إدارة الطلاب
        Route::prefix('students')->name('students.')->group(function () {
            // 4.4.4.4 عرض جميع الطلاب مع فلترة
            Route::get('/', [StudentsController::class, 'index'])->name('index');

            // 4.4.4.1 إضافة طالب (عرض النموذج + حفظ)
            Route::get('create', [StudentsController::class, 'create'])->name('create');
            Route::post('/', [StudentsController::class, 'store'])->name('store');

            // 4.4.4.5 عرض طالب محدد
            Route::get('{student}', [StudentsController::class, 'show'])->name('show');

            // 4.4.4.2 تعديل بيانات طالب (عرض النموذج + حفظ)
            Route::get('{student}/edit', [StudentsController::class, 'edit'])->name('edit');
            Route::put('{student}', [StudentsController::class, 'update'])->name('update');

            // 4.4.4.3 حذف/تعطيل طالب
            Route::delete('{student}', [StudentsController::class, 'destroy'])->name('destroy');
        });


        // إدارة المدرّسين
        Route::prefix('teachers')->name('teachers.')->group(function(){
            Route::get('/',            [TeachersController::class,'index'])->name('index');
            Route::get('create',       [TeachersController::class,'create'])->name('create');
            Route::post('/',           [TeachersController::class,'store'])->name('store');
            Route::get('{teacher}',    [TeachersController::class,'show'])->name('show');
            Route::get('{teacher}/edit',[TeachersController::class,'edit'])->name('edit');
            Route::put('{teacher}',    [TeachersController::class,'update'])->name('update');
            Route::delete('{teacher}', [TeachersController::class,'destroy'])->name('destroy');
        });

        // إدارة المواد
        Route::prefix('subjects')->name('subjects.')->group(function() {
            // قائمة المواد مع بحث وفرز
            Route::get('/', [SubjectsController::class, 'index'])->name('index');
            // نموذج إضافة مادة
            Route::get('create', [SubjectsController::class, 'create'])->name('create');
            // حفظ المادة الجديدة
            Route::post('/', [SubjectsController::class, 'store'])->name('store');
            // عرض تفاصيل مادة
            Route::get('{subject}', [SubjectsController::class, 'show'])->name('show');
            // نموذج تعديل مادة
            Route::get('{subject}/edit', [SubjectsController::class, 'edit'])->name('edit');
            // تحديث بيانات المادة
            Route::put('{subject}', [SubjectsController::class, 'update'])->name('update');
            // تعطيل/تفعيل المادة
            Route::delete('{subject}', [SubjectsController::class, 'destroy'])->name('destroy');

        });
    });

Route::patch('manager/subjects/{subject}/toggle', [SubjectsController::class,'toggle'])
    ->name('manager.subjects.toggle')
    ->middleware(['auth','role:institute manager']);



Route::get('manager/schedules', [SessionScheduleController::class,'index'])
    ->name('manager.schedules.index')
    ->middleware(['auth','role:institute manager']);













//////////////////////////////////////////////////////////////////

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

     Route::get('schedule', [ScheduleController::class, 'index'])
          ->name('schedule.index');

     Route::get('students', [StudentController::class, 'index'])
          ->name('students.index');

     // هنا نضيف المسار لصفحة التفاصيل
     Route::get('students/{student}', [StudentController::class, 'show'])
          ->name('students.show');

     // إزالة طالب من صف
     Route::delete(
          'classes/{class}/students/{student}',
          [StudentController::class, 'removeStudent']
     )->name('classes.students.remove');

     // 1) عرض نموذج إضافة امتحان لطالب في صف معين
     Route::get(
          'classes/{class}/students/{student}/exams/create',
          [ExamController::class, 'create']
     )->name('classes.students.exams.create');

     // 2) تخزين الامتحان
     Route::post(
          'classes/{class}/students/{student}/exams',
          [ExamController::class, 'store']
     )->name('classes.students.exams.store');

     // عرض جميع الحلقات (الصفوف)
     Route::get('classes', [ClassController::class, 'index'])
          ->name('classes.index');

     // (اختياري) عرض تفاصيل حلقة
     Route::get('classes/{class}', [ClassController::class, 'show'])
          ->name('classes.show');

     Route::get('classes/{class}/students', [ClassController::class, 'students'])
          ->name('classes.students');
});



// routes/web.php

use App\Http\Controllers\SuperAdmin\InstituteController;

Route::middleware(['auth','role:super admin'])
    ->prefix('super-admin/institutes')
    ->name('super-admin.institutes.')
    ->group(function() {
        // عرض نموذج إنشاء مدير معهد منفصل
        Route::get('manager/create',      [InstituteController::class, 'createManager'])
            ->name('manager.create');
        Route::post('manager/store',      [InstituteController::class, 'storeManager'])
            ->name('manager.store');

        // إدارة المعاهد
        Route::get('/',            [InstituteController::class, 'index'])->name('index');
        Route::get('create',       [InstituteController::class, 'create'])->name('create');
        Route::post('/',           [InstituteController::class, 'store'])->name('store');
        Route::get('{institute}',  [InstituteController::class, 'show'])->name('show');
        Route::get('{institute}/edit',   [InstituteController::class, 'edit'])->name('edit');
        Route::put('{institute}',  [InstituteController::class, 'update'])->name('update');
        Route::delete('{institute}', [InstituteController::class, 'destroy'])->name('destroy');
    });

