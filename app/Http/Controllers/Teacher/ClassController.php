<?php
// app/Http/Controllers/Teacher/ClassController.php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * عرض جميع الصفوف الخاصة بالمعلم
     */
    public function index(Request $request)
    {
        $teacherId = auth()->id();

        $query = EducationClass::where('user_id', $teacherId);

        // دعم فلترة بحث بسيطة
        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // paginate 10 per page
        $classes = $query->orderBy('id','desc')
                         ->paginate(10)
                         ->withQueryString();

        return view('teacher.classes.index', compact('classes'));
    }

    /**
     * (اختياري) عرض تفاصيل حلقة
     */
    public function show(EducationClass $class)
    {
        // تأكد ملكية المعلم
        if ($class->user_id !== auth()->id()) {
            abort(403);
        }
        // جلب الطلاب والامتحانات إن أردت
        $class->load('users','exams');
        return view('teacher.classes.show', compact('class'));
    }

        /**
     * عرض طلاب حلقة محددة
     */
    public function students(\App\Models\EducationClass $class)
    {
        // تأكد أن هذه الحلقة تخصّ المدرّس الحالي
        if ($class->user_id !== auth()->id()) {
            abort(403);
        }

        // جلب الطلاب المسجلين
        $students = $class->users()->with('student')->get();

        return view('teacher.classes.students', compact('class', 'students'));
    }
}
