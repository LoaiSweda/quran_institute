<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\Manager\GuardiansController;
use App\Http\Controllers\Manager\SessionScheduleController;
use App\Http\Controllers\Manager\StudentsController;
use App\Http\Controllers\Manager\TeachersController;
use App\Http\Controllers\SuperAdminController;
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
use App\Http\Controllers\SuperAdmin\InstituteManagerController;
use App\Http\Controllers\Manager\CertificateRequestController as ManagerCertificateRequestController;


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\AnnouncementController as ManagerAnnouncementController;
use App\Http\Controllers\Manager\AnnouncementInboxController as ManagerAnnouncementInboxController;

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
    ->group(function () {
        Route::get('dashboard', [App\Http\Controllers\SuperAdminController::class, 'showInstitutes'])
            ->name('super-admin.institutes');
        Route::get('dashboard/{institute}', [SuperAdminController::class, 'showInstituteDetails'])
            ->name('super-admin.institutes.Details'); // غير show إلى Details

        // مسارات الصفحات التفصيلية
        Route::get('dashboard/{institute}/students', [SuperAdminController::class, 'showInstituteStudents'])
            ->name('super-admin.institutes.students');
        Route::get('dashboard/{institute}/teachers', [SuperAdminController::class, 'showInstituteTeachers'])
            ->name('super-admin.institutes.teachers');
        Route::get('dashboard/{institute}/classes', [SuperAdminController::class, 'showInstituteClasses'])
            ->name('super-admin.institutes.classes');
        Route::get('dashboard/{institute}/subjects', [SuperAdminController::class, 'showInstituteSubjects'])
            ->name('super-admin.institutes.subjects');
        Route::get('dashboard/{institute}/exams', [SuperAdminController::class, 'showInstituteExams'])
            ->name('super-admin.institutes.exams');
        Route::get('dashboard/{institute}/guardians', [SuperAdminController::class, 'showInstituteGuardians'])
            ->name('super-admin.institutes.guardians');
        Route::get('dashboard/{institute}/schedules', [SuperAdminController::class, 'showInstituteSchedules'])
            ->name('super-admin.institutes.schedules');
        Route::get('dashboard/{institute}/memorizations', [SuperAdminController::class, 'showInstituteMemorizations'])
            ->name('super-admin.institutes.memorizations');
    });Route::middleware(['auth','role:admin'])




     ->prefix('admin')
     ->group(fn() => Route::view('dashboard','dashboards.admin'));



Route::middleware(['auth', 'role:institute manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        // لوحة التحكم
        Route::view('dashboard', 'dashboards.manager')->name('dashboard');

        Route::get('profile', [\App\Http\Controllers\Manager\ProfileController::class, 'show'])
            ->name('profile.show');
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

// إدارة الـ Admins من قبل مدير المعهد
        Route::prefix('admins')->name('admins.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Manager\AdminsController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Manager\AdminsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Manager\AdminsController::class, 'store'])->name('store');
            Route::get('{admin}', [\App\Http\Controllers\Manager\AdminsController::class, 'show'])->name('show');
            Route::get('{admin}/edit', [\App\Http\Controllers\Manager\AdminsController::class, 'edit'])->name('edit');
            Route::put('{admin}', [\App\Http\Controllers\Manager\AdminsController::class, 'update'])->name('update');
            Route::delete('{admin}', [\App\Http\Controllers\Manager\AdminsController::class, 'destroy'])->name('destroy');
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
        // Library Routes for Institute Manager
        Route::prefix('library')->name('library.')->group(function()
        {
                Route::get('/', [LibraryController::class, 'index'])->name('index');
                Route::get('/library/api', [LibraryController::class, 'apiIndex'])->name('api.index');
                Route::get('create', [LibraryController::class, 'create'])->name('create');
                Route::post('/', [LibraryController::class, 'store'])->name('store');
                Route::get('{library}/edit', [LibraryController::class, 'edit'])->name('edit');
                Route::put('{library}', [LibraryController::class, 'update'])->name('update');
                Route::delete('{library}', [LibraryController::class, 'destroy'])->name('destroy');
                Route::patch('{library}/toggle-visibility', [LibraryController::class, 'toggleVisibility'])->name('toggle-visibility');
        });
        Route::get('announcements/inbox',      [ManagerAnnouncementInboxController::class, 'index'])->name('announcements.inbox');
        Route::get('announcements/inbox/{ad}', [ManagerAnnouncementInboxController::class, 'show'])->name('announcements.inbox.show');


        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/',                 [ManagerAnnouncementController::class,'index'])->name('index');
            Route::post('/',                [ManagerAnnouncementController::class,'store'])->name('store');
            Route::get('{ad}',              [ManagerAnnouncementController::class,'show'])
                ->whereNumber('ad')->name('show');
            Route::get('{ad}/edit',         [ManagerAnnouncementController::class,'edit'])
                ->whereNumber('ad')->name('edit');
            Route::put('{ad}',              [ManagerAnnouncementController::class,'update'])
                ->whereNumber('ad')->name('update');
            Route::delete('{ad}',           [ManagerAnnouncementController::class,'destroy'])
                ->whereNumber('ad')->name('destroy');
        });

        Route::prefix('certificates')->name('certificates.')->group(function () {
            // List + request form
            Route::get('/', [ManagerCertificateRequestController::class, 'index'])->name('index');
            // Create a new request (subject must be finished)
            Route::post('/', [ManagerCertificateRequestController::class, 'store'])->name('store');
            // Export (enabled only when approved)
            Route::get('{certificateRequest}/export', [ManagerCertificateRequestController::class, 'export'])
                ->name('export');
        });

    });



Route::patch('manager/subjects/{subject}/toggle', [SubjectsController::class,'toggle'])
    ->name('manager.subjects.toggle')
    ->middleware(['auth','role:institute manager']);



Route::get('manager/schedules', [SessionScheduleController::class,'index'])
    ->name('manager.schedules.index')
    ->middleware(['auth','role:institute manager']);













//////////////////////////////////////////////////////////////////
use App\Http\Controllers\Teacher\AnnouncementInboxController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;

Route::middleware(['auth','role:teacher'])
     ->prefix('teacher')
     ->name('teacher.')
     ->group(function () {

     // لوحة المعلم
     Route::view('dashboard','dashboards.teacher')->name('dashboard');

         Route::get('profile', [TeacherProfileController::class, 'show'])->name('profile.show');


         Route::get('announcements/inbox',       [AnnouncementInboxController::class, 'index'])->name('announcements.inbox');
         Route::get('announcements/inbox/{ad}',  [AnnouncementInboxController::class, 'show'])->name('announcements.inbox.show');
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
         // Library Routes for Institute Manager
     Route::prefix('library')->name('library.')->group(function()
     {
             Route::get('/', [LibraryController::class, 'index'])->name('index');
             Route::get('/library/api', [LibraryController::class, 'apiIndex'])->name('api.index');
             Route::get('create', [LibraryController::class, 'create'])->name('create');
             Route::post('/', [LibraryController::class, 'store'])->name('store');
             Route::get('{library}/edit', [LibraryController::class, 'edit'])->name('edit');
             Route::put('{library}', [LibraryController::class, 'update'])->name('update');
             Route::delete('{library}', [LibraryController::class, 'destroy'])->name('destroy');
     });
});




Route::middleware(['auth','role:super admin'])
    ->prefix('super-admin/managers')
    ->name('super-admin.managers.')
    ->group(function() {
        Route::get('/',            [InstituteManagerController::class, 'index'])->name('index');
        Route::get('create',       [InstituteManagerController::class, 'create'])->name('create');
        Route::post('/',           [InstituteManagerController::class, 'store'])->name('store');
        Route::get('{user}',      [InstituteManagerController::class, 'show'])->name('show');
        Route::get('{user}/edit',  [InstituteManagerController::class, 'edit'])->name('edit');
        Route::put('{user}',       [InstituteManagerController::class, 'update'])->name('update');
        Route::delete('{user}',    [InstituteManagerController::class, 'destroy'])->name('destroy');
        Route::post('{user}/unassign-institute', [InstituteManagerController::class, 'unassignInstitute'])->name('unassign-institute');

     });



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


        Route::prefix('library')->name('library.')->group(function()
        {
            Route::get('/', [LibraryController::class, 'index'])->name('index');
            Route::get('/library/api', [LibraryController::class, 'apiIndex'])->name('api.index');
            Route::get('create', [LibraryController::class, 'create'])->name('create');
            Route::post('/', [LibraryController::class, 'store'])->name('store');
            Route::get('{library}/edit', [LibraryController::class, 'edit'])->name('edit');
            Route::put('{library}', [LibraryController::class, 'update'])->name('update');
            Route::delete('{library}', [LibraryController::class, 'destroy'])->name('destroy');
        });
    });

//////////////////////////////////////////////////////////////////////////////////////////////////////
    Route::middleware(['auth','role:admin'])
          ->get('/attendance/scan', [AttendanceScanController::class, 'show'])
          ->name('attendance.scan');

     Route::middleware(['auth','role:admin'])
          ->post('/attendance/scan', [AttendanceController::class,'scan'])
          ->name('attendance.scan.post');


   // إضافة مسار جديد لتسجيل الغياب
     Route::middleware(['auth','role:admin'])
          ->post('/attendance/mark-absent', [AttendanceController::class, 'markAbsent'])
          ->name('attendance.mark.absent');

     Route::middleware(['auth','role:admin'])
          ->get('/attendance/status', [AttendanceController::class, 'attendanceStatus'])
          ->name('attendance.status');

     Route::middleware(['auth','role:admin'])
          ->get('/attendance/session-status', [AttendanceController::class, 'sessionStatus'])
          ->name('attendance.session-status');



use App\Http\Controllers\supervisor\AnnouncementInboxController as AdminAnnouncementInboxController;

use App\Http\Controllers\supervisor\GuardiansController as SupervisorGuardiansController;
use App\Http\Controllers\supervisor\SubjectsController as SupervisorSubjectsController;
use App\Http\Controllers\supervisor\TeachersController as SupervisorTeachersController;
use App\Http\Controllers\supervisor\StudentsController as supervisorStudentsController;
use App\Http\Controllers\supervisor\ClassSchedulesController as SupervisorClassSchedulesController;
use App\Http\Controllers\supervisor\SchedulesController as SupervisorSchedulesController;
use App\Http\Controllers\supervisor\ClasStudentsController as AdminClasStudentsController;
use App\Http\Controllers\supervisor\AnnouncementController as AdminAnnouncementController;

Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {


         Route::prefix('library')->name('library.')->group(function()
        {
                Route::get('/', [LibraryController::class, 'index'])->name('index');
                Route::get('/library/api', [LibraryController::class, 'apiIndex'])->name('api.index');
                Route::get('create', [LibraryController::class, 'create'])->name('create');
                Route::post('/', [LibraryController::class, 'store'])->name('store');
                Route::get('{library}/edit', [LibraryController::class, 'edit'])->name('edit');
                Route::put('{library}', [LibraryController::class, 'update'])->name('update');
                Route::delete('{library}', [LibraryController::class, 'destroy'])->name('destroy');
                Route::patch('{library}/toggle-visibility', [LibraryController::class, 'toggleVisibility'])->name('toggle-visibility');
        });

        Route::get('profile', [\App\Http\Controllers\supervisor\ProfileController::class, 'show'])
            ->name('profile.show');

        // مواد المشرف
        Route::get('subjects',              [SupervisorSubjectsController::class, 'index'])->name('subjects.index');
        Route::get('subjects/{subject}',    [SupervisorSubjectsController::class, 'show'])->name('subjects.show');

        // المدرّسون للمشرف
        Route::get('teachers',              [SupervisorTeachersController::class, 'index'])->name('teachers.index');
        Route::get('teachers/{teacher}',    [SupervisorTeachersController::class, 'show'])->name('teachers.show');

        // إدارة الطلاب (نفس عمليات المدير)
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/',                [supervisorStudentsController::class, 'index'])->name('index');
            Route::get('create',           [supervisorStudentsController::class, 'create'])->name('create');
            Route::post('/',               [supervisorStudentsController::class, 'store'])->name('store');
            Route::get('{student}',        [supervisorStudentsController::class, 'show'])->name('show');
            Route::get('{student}/edit',   [supervisorStudentsController::class, 'edit'])->name('edit');
            Route::put('{student}',        [supervisorStudentsController::class, 'update'])->name('update');
            Route::delete('{student}',     [supervisorStudentsController::class, 'destroy'])->name('destroy');

        });


        // إدارة أولياء الأمور (CRUD)
        Route::prefix('guardians')->name('guardians.')->group(function () {
            Route::get('/',              [SupervisorGuardiansController::class, 'index'])->name('index');
            Route::get('create',         [SupervisorGuardiansController::class, 'create'])->name('create');
            Route::post('/',             [SupervisorGuardiansController::class, 'store'])->name('store');
            Route::get('{guardian}',     [SupervisorGuardiansController::class, 'show'])->name('show');
            Route::get('{guardian}/edit',[SupervisorGuardiansController::class, 'edit'])->name('edit');
            Route::put('{guardian}',     [SupervisorGuardiansController::class, 'update'])->name('update');
            Route::delete('{guardian}',  [SupervisorGuardiansController::class, 'destroy'])->name('destroy');
        });
        // إدارة الحلقات (Classes = الحلقات) للمشرف
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/',            [\App\Http\Controllers\supervisor\ClassesController::class, 'index'])->name('index');
            Route::get('create',       [\App\Http\Controllers\supervisor\ClassesController::class, 'create'])->name('create');
            Route::post('/',           [\App\Http\Controllers\supervisor\ClassesController::class, 'store'])->name('store');
            Route::get('{class}',      [\App\Http\Controllers\supervisor\ClassesController::class, 'show'])->name('show');

            Route::get('{class}/students',            [AdminClasStudentsController::class,'index'])  ->name('students.index');
            Route::get('{class}/students/create',     [AdminClasStudentsController::class,'create']) ->name('students.create');
            Route::post('{class}/students',           [AdminClasStudentsController::class,'store'])  ->name('students.store');
            Route::delete('{class}/students/{user}',  [AdminClasStudentsController::class,'destroy'])->name('students.destroy');


            Route::get('{class}/edit', [\App\Http\Controllers\supervisor\ClassesController::class, 'edit'])->name('edit');
            Route::put('{class}',      [\App\Http\Controllers\supervisor\ClassesController::class, 'update'])->name('update');
            Route::delete('{class}',   [\App\Http\Controllers\supervisor\ClassesController::class, 'destroy'])->name('destroy');
        });
        // إدارة جداول مواعيد الحلقة (Schedules) للمشرف
        Route::prefix('classes/{class}/schedules')
            ->name('classes.schedules.')
            ->group(function () {
                Route::get('create',            [SupervisorClassSchedulesController::class, 'create'])->name('create');
                Route::post('/',                [SupervisorClassSchedulesController::class, 'store'])->name('store');
                Route::get('{schedule}/edit',   [SupervisorClassSchedulesController::class, 'edit'])->name('edit');
                Route::put('{schedule}',        [SupervisorClassSchedulesController::class, 'update'])->name('update');
                Route::delete('{schedule}',     [SupervisorClassSchedulesController::class, 'destroy'])->name('destroy');
            });


        Route::get('announcements/inbox',      [AdminAnnouncementInboxController::class, 'index'])->name('announcements.inbox');
        Route::get('announcements/inbox/{ad}', [AdminAnnouncementInboxController::class, 'show'])->name('announcements.inbox.show');

        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/',                 [AdminAnnouncementController::class,'index'])->name('index');
            Route::post('/',                [AdminAnnouncementController::class,'store'])->name('store');
            Route::get('{ad}',              [AdminAnnouncementController::class,'show'])->name('show');
            Route::get('{ad}/edit',         [AdminAnnouncementController::class,'edit'])->name('edit');
            Route::put('{ad}',              [AdminAnnouncementController::class,'update'])->name('update');
            Route::delete('{ad}',           [AdminAnnouncementController::class,'destroy'])->name('destroy');
        });

        Route::get('schedules', [SupervisorSchedulesController::class,'index'])
            ->name('schedules.index');
    });



          /*

          كنت عم اشتغل بقصة ال session_count بس ما تخزنت بس بتعرض لازم اخرنها
          والطالب بس يسجل بمحاضرة معينة
          ويرجع يسجل الاسبوع الجايي بنفس اليوم ما بيرضى

          */
