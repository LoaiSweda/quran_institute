<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function create(EducationClass $class, User $student)
    {
        if ($class->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $class->users()->where('user_id', $student->id)->exists()) {
            abort(403);
        }

        return view('teacher.exams.create', compact('class','student'));
    }

    public function store(Request $request, EducationClass $class, User $student)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'notes'  => 'nullable|string',
            'points' => 'required|numeric|min:0',
            'degree' => 'required|numeric|min:0',
        ]);

        $exam = Exam::create([
            'class_id'   => $class->id,
            'student_id' => $student->student->id,
            'name'       => $request->name,
            'notes'      => $request->notes,
            'points'     => $request->points,
            'degree'     => $request->degree,
        ]);

        $studentModel = $student->student;
        $studentModel->points += $exam->points;
        $studentModel->save();

        return redirect()
            ->route('teacher.classes.students.exams.create', [
                'class'   => $class->id,
                'student' => $student->id,
            ])
            ->with('success', 'تم إضافة الامتحان وتحديث النقاط.');
    }

}
