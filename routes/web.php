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
use App\Http\Controllers\supervisor\AttendanceScanController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Manager\ClassesController;
use App\Http\Controllers\Manager\ClasStudentsController;
use App\Http\Controllers\Manager\SubjectsController;
use App\Http\Controllers\SuperAdmin\InstituteController;

use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])
     ->name('home')
     ->middleware('guest');

Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');

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

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth','role:super admin'])
     ->prefix('super-admin')
     ->group(fn() => Route::view('dashboard','dashboards.super_admin'));

Route::middleware(['auth','role:admin'])
     ->prefix('admin')
     ->group(fn() => Route::view('dashboard','dashboards.admin'));


Route::middleware(['auth', 'role:institute manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

          Route::view('dashboard', 'dashboards.manager')->name('dashboard');

          Route::prefix('classes')->name('classes.')->group(function(){

          Route::get('/', [ClassesController::class,'index'])->name('index');
          Route::get('create', [ClassesController::class,'create'])->name('create');
          Route::post('/', [ClassesController::class,'store'])->name('store');

          Route::get('{class}/students',          [ClasStudentsController::class,'index'])  ->name('students.index');
          Route::get('{class}/students/create',   [ClasStudentsController::class,'create'])->name('students.create');
          Route::post('{class}/students',         [ClasStudentsController::class,'store']) ->name('students.store');
          Route::delete('{class}/students/{user}',[ClasStudentsController::class,'destroy'])->name('students.destroy');

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


          Route::prefix('guardians')->name('guardians.')->group(function () {
               Route::get('/', [GuardiansController::class, 'index'])->name('index');

               Route::get('create', [GuardiansController::class, 'create'])->name('create');
               Route::post('/', [GuardiansController::class, 'store'])->name('store');

               Route::get('{guardian}', [GuardiansController::class, 'show'])->name('show');

               Route::get('{guardian}/edit', [GuardiansController::class, 'edit'])->name('edit');
               Route::put('{guardian}', [GuardiansController::class, 'update'])->name('update');

               Route::delete('{guardian}', [GuardiansController::class, 'destroy'])->name('destroy');
          });


          Route::prefix('students')->name('students.')->group(function () {
               Route::get('/', [StudentsController::class, 'index'])->name('index');

               Route::get('create', [StudentsController::class, 'create'])->name('create');
               Route::post('/', [StudentsController::class, 'store'])->name('store');

               Route::get('{student}', [StudentsController::class, 'show'])->name('show');

               Route::get('{student}/edit', [StudentsController::class, 'edit'])->name('edit');
               Route::put('{student}', [StudentsController::class, 'update'])->name('update');

               Route::delete('{student}', [StudentsController::class, 'destroy'])->name('destroy');
          });


          Route::prefix('teachers')->name('teachers.')->group(function() {

               Route::get('new-user', [TeachersController::class,'createUser'])->name('newUser');
               Route::post('new-user',[TeachersController::class,'storeUser' ])->name('storeUser');
               Route::get('/', [TeachersController::class, 'index'])->name('index');
               Route::get('create', [TeachersController::class, 'create'])->name('create');
               Route::post('/',    [TeachersController::class, 'store'])->name('store');
               Route::get('{teacher}',       [TeachersController::class, 'show'])->name('show');
               Route::get('{teacher}/edit',  [TeachersController::class, 'edit'])->name('edit');
               Route::put('{teacher}',       [TeachersController::class, 'update'])->name('update');
               Route::delete('{teacher}',    [TeachersController::class, 'destroy'])->name('destroy');
          });


          Route::prefix('subjects')->name('subjects.')->group(function() {
               Route::get('/', [SubjectsController::class, 'index'])->name('index');
               Route::get('create', [SubjectsController::class, 'create'])->name('create');
               Route::post('/', [SubjectsController::class, 'store'])->name('store');
               Route::get('{subject}', [SubjectsController::class, 'show'])->name('show');
               Route::get('{subject}/edit', [SubjectsController::class, 'edit'])->name('edit');
               Route::put('{subject}', [SubjectsController::class, 'update'])->name('update');
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

          Route::view('dashboard','dashboards.teacher')->name('dashboard');

          Route::get('announcements', [AnnouncementController::class,'index'])
               ->name('announcements.index');

          Route::get('announcements/{ad}', [AnnouncementController::class,'show'])
               ->name('announcements.show');

          Route::get('announcements/{ad}/edit', [AnnouncementController::class,'edit'])
               ->name('announcements.edit');

          Route::put('announcements/{ad}', [AnnouncementController::class,'update'])
               ->name('announcements.update');

          Route::delete('announcements/{ad}', [AnnouncementController::class,'destroy'])
               ->name('announcements.destroy');

          Route::post('announcements', [AnnouncementController::class,'store'])
               ->name('announcements.store');

          Route::get('schedule', [ScheduleController::class, 'index'])
               ->name('schedule.index');

          Route::get('students', [StudentController::class, 'index'])
               ->name('students.index');

          Route::get('students/{student}', [StudentController::class, 'show'])
               ->name('students.show');

          Route::delete('classes/{class}/students/{student}',[StudentController::class, 'removeStudent'])
               ->name('classes.students.remove');

          Route::get('classes/{class}/students/{student}/exams/create',[ExamController::class, 'create'])
               ->name('classes.students.exams.create');

          Route::post('classes/{class}/students/{student}/exams',[ExamController::class, 'store'])
               ->name('classes.students.exams.store');

          Route::get('classes', [ClassController::class, 'index'])
               ->name('classes.index');

          Route::get('classes/{class}', [ClassController::class, 'show'])
               ->name('classes.show');

          Route::get('classes/{class}/students', [ClassController::class, 'students'])
               ->name('classes.students');
});



Route::middleware(['auth','role:super admin'])
    ->prefix('super-admin/institutes')
    ->name('super-admin.institutes.')
    ->group(function() {
          Route::get('manager/create',      [InstituteController::class, 'createManager'])
               ->name('manager.create');
          Route::post('manager/store',      [InstituteController::class, 'storeManager'])
               ->name('manager.store');

          Route::get('/',            [InstituteController::class, 'index'])->name('index');
          Route::get('create',       [InstituteController::class, 'create'])->name('create');
          Route::post('/',           [InstituteController::class, 'store'])->name('store');
          Route::get('{institute}',  [InstituteController::class, 'show'])->name('show');
          Route::get('{institute}/edit',   [InstituteController::class, 'edit'])->name('edit');
          Route::put('{institute}',  [InstituteController::class, 'update'])->name('update');
          Route::delete('{institute}', [InstituteController::class, 'destroy'])->name('destroy');
     });


     

Route::middleware(['auth','role:admin'])
     ->get('/attendance/scan', [AttendanceScanController::class, 'show'])
     ->name('attendance.scan');

Route::middleware(['auth','role:admin'])
     ->post('/attendance/scan', [AttendanceController::class,'scan'])
     ->name('attendance.scan.post');
     

Route::middleware(['auth','role:admin'])
     ->post('/attendance/mark-absent', [AttendanceController::class, 'markAbsent'])
     ->name('attendance.mark.absent');

Route::middleware(['auth','role:admin'])
     ->get('/attendance/status', [AttendanceController::class, 'attendanceStatus'])
     ->name('attendance.status');

Route::middleware(['auth','role:admin'])
     ->get('/attendance/session-status', [AttendanceController::class, 'sessionStatus'])
     ->name('attendance.session-status');
























          /*

          كنت عم اشتغل بقصة ال session_count بس ما تخزنت بس بتعرض لازم اخرنها 
          والطالب بس يسجل بمحاضرة معينة 
          ويرجع يسجل الاسبوع الجايي بنفس اليوم ما بيرضى 

          */