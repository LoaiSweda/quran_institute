<?php

namespace App\Http\Controllers;

use App\Models\Institute;
use App\Models\SessionSchedule;
use App\Models\Exam;
use App\Models\Guardian;
use App\Models\StudentProgress;
use App\Models\EducationClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function showInstitutes()
    {
        // جلب جميع المعاهد مع العلاقات المطلوبة بما فيها مدير المعهد وبياناته
        $institutes = Institute::with(['manager.admin', 'subjects', 'teachers'])
            ->withCount(['studentUsers as students_count', 'subjects as subjects_count', 'teachers as teachers_count'])
            ->get();

        return view('dashboards.super_admin', compact('institutes'));
    }

    // طريقة جديدة لعرض تفاصيل معهد معين
    public function showInstituteDetails(Institute $institute)
    {
        // تحميل جميع العلاقات اللازمة مع العد
        $institute->load([
            'manager.admin',
            'subjects',
            'teachers.user',
            'studentUsers.student',
            'admins.admin',
            'classes.subject',
            'classes.teacher.user',
            'classes.students.user',
            'classes.sessionSchedules'
        ])->loadCount([
            'students',
            'subjects',
            'teachers',
            'classes',
            'admins'
        ]);

        // جلب إحصائيات إضافية
        $totalSessions = SessionSchedule::whereHas('educationClass.subject', function($query) use ($institute) {
            $query->where('institute_id', $institute->id);
        })->count();

        $totalExams = Exam::whereHas('educationClass.subject', function($query) use ($institute) {
            $query->where('institute_id', $institute->id);
        })->count();

        $totalGuardians = Guardian::whereHas('user.institutes', function($query) use ($institute) {
            $query->where('institute_id', $institute->id)
                ->where('role_institute', 'guardian');
        })->count();

        $attendanceRate = $institute->students->avg('present_percentage') ?? 0;

        // حساب إجمالي التسميعات
        $totalMemorizations = StudentProgress::whereHas('educationClass.subject', function($query) use ($institute) {
            $query->where('institute_id', $institute->id);
        })->sum('number_sessions_attended');

        // إحصائيات إضافية
        $stats = [
            'active_subjects' => $institute->subjects->where('is_active', true)->count(),
            'total_classes' => $institute->classes_count,
            'total_sessions' => $totalSessions,
            'active_teachers' => $institute->teachers->count(),
            'total_exams' => $totalExams,
            'total_guardians' => $totalGuardians,
            'attendance_rate' => round($attendanceRate, 2),
            'total_memorizations' => $totalMemorizations,
        ];

        return view('super-admin.institutes.Details', compact('institute', 'stats'));
    }

    public function showInstituteStudents(Institute $institute)
    {
        $students = $institute->students()->with(['user', 'classes', 'guardian.user'])->get();
        return view('super-admin.institutes.students', compact('institute', 'students'));
    }

    public function showInstituteTeachers(Institute $institute)
    {
        $teachers = $institute->teachers()->with(['user', 'teachingClasses.subject'])->get();
        return view('super-admin.institutes.teachers', compact('institute', 'teachers'));
    }

    public function showInstituteClasses(Institute $institute)
    {
        $classes = EducationClass::whereHas('subject', function($query) use ($institute) {
            $query->where('institute_id', $institute->id);
        })->with(['subject', 'teacher.user', 'students', 'sessionSchedules'])->get();

        return view('super-admin.institutes.classes', compact('institute', 'classes'));
    }

    public function showInstituteSubjects(Institute $institute)
    {
        $subjects = $institute->subjects()->with(['classes.teacher.user', 'classes.students'])->get();
        return view('super-admin.institutes.subjects', compact('institute', 'subjects'));
    }

    public function showInstituteExams(Institute $institute)
    {
        $exams = Exam::whereHas('educationClass.subject', function($query) use ($institute) {
            $query->where('institute_id', $institute->id);
        })->with(['educationClass.subject', 'student.user'])->get();

        return view('super-admin.institutes.exams', compact('institute', 'exams'));
    }

    public function showInstituteGuardians(Institute $institute)
    {
        $guardians = Guardian::whereHas('user.institutes', function($query) use ($institute) {
            $query->where('institute_id', $institute->id)
                ->where('role_institute', 'guardian');
        })->with(['user', 'students.user'])->get();

        return view('super-admin.institutes.guardians', compact('institute', 'guardians'));
    }

    public function showInstituteSchedules(Institute $institute)
    {
        $schedules = SessionSchedule::whereHas('educationClass.subject', function($query) use ($institute) {
            $query->where('institute_id', $institute->id);
        })->with(['educationClass.subject', 'educationClass.teacher.user'])->get();

        return view('super-admin.institutes.schedules', compact('institute', 'schedules'));
    }

    public function showInstituteMemorizations(Institute $institute)
    {
        $memorizations = StudentProgress::whereHas('educationClass.subject', function($query) use ($institute) {
            $query->where('institute_id', $institute->id);
        })->with(['student.user', 'educationClass.subject'])->get();

        return view('super-admin.institutes.memorizations', compact('institute', 'memorizations'));
    }
}
