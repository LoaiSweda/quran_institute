<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;         
use App\Models\EducationClass;            
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        $classes = EducationClass::where('user_id', $teacherId)
                                              ->with('users')
                                              ->get();

        $students = $classes->pluck('users')
                            ->flatten()
                            ->unique('id')
                            ->values();

        return view('teacher.students.index', compact('classes','students'));
    }

  
    public function show(User $student)
    {
        $teacherId = auth()->id();

        $teacherClassIds = EducationClass::where('user_id', $teacherId)
                            ->pluck('id')
                            ->toArray();

        $belongs = $student->educationClasses()  
                        ->whereIn('class_id', $teacherClassIds)
                        ->exists();

        if (! $belongs) {
            abort(403, 'غير مخوّل للوصول إلى بيانات هذا الطالب.');
        }

        $profile = $student->student()->with('guardian')->first();

        return view('teacher.students.show', compact('student', 'profile'));
    }

    public function removeStudent(EducationClass $class, User $student)
    {
        if ($class->user_id !== auth()->id()) {
            abort(403, 'غير مخوّل لإدارة هذا الصف.');
        }

        if (! $class->users()->where('user_id', $student->id)->exists()) {
            return back()->with('error', 'هذا الطالب غير مرتبط بهذا الصف.');
        }

        $class->users()->detach($student->id);

        return back()->with('success', "تمت إزالة الطالب {$student->first_name} من الصف {$class->name}.");
    }

}
