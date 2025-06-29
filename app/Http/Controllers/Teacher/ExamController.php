<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // عرض نموذج إنشاء امتحان/تسميع
    public function create(EducationClass $class, User $student)
    {
        // تأكد صلاحية المعلم
        if ($class->user_id !== auth()->id()) {
            abort(403);
        }

        // تأكد أن الطالب مرتبط بالصف
        if (! $class->users()->where('user_id', $student->id)->exists()) {
            abort(403);
        }

        return view('teacher.exams.create', compact('class','student'));
    }

   // تخزين الامتحان
    public function store(Request $request, EducationClass $class, User $student)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'notes'  => 'nullable|string',
            'points' => 'required|numeric|min:0',
            'degree' => 'required|numeric|min:0',
        ]);

        // إنشاء الامتحان
        $exam = Exam::create([
            'class_id'   => $class->id,
            'student_id' => $student->student->id,
            'name'       => $request->name,
            'notes'      => $request->notes,
            'points'     => $request->points,
            'degree'     => $request->degree,
        ]);

        // تحديث مجموع النقاط
        $studentModel = $student->student;
        $studentModel->points += $exam->points;
        $studentModel->save();

        // إعادة التوجيه إلى صفحة إنشاء الامتحان (التي تحتوي على جدول الامتحانات)
        return redirect()
            ->route('teacher.classes.students.exams.create', [
                'class'   => $class->id,
                'student' => $student->id,
            ])
            ->with('success', 'تم إضافة الامتحان وتحديث النقاط.');
    }

}
