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

  
    /**
     * عرض تفاصيل طالب محدد
     */
    public function show(User $student)
    {
        $teacherId = auth()->id();

        // 1) جلب معرفات الصفوف التي يملكها هذا المعلم
        $teacherClassIds = EducationClass::where('user_id', $teacherId)
                            ->pluck('id')
                            ->toArray();

        // 2) تأكد أن الطالب موجود في واحدة من هذه الصفوف
        $belongs = $student->educationClasses()   // علاقة الطالب بالصفوف (pivot)
                        ->whereIn('class_id', $teacherClassIds)
                        ->exists();

        if (! $belongs) {
            abort(403, 'غير مخوّل للوصول إلى بيانات هذا الطالب.');
        }

        // 3) جلب سجلّ الطالب الإضافي
        $profile = $student->student()->with('guardian')->first();

        // 4) عرض الـ View
        return view('teacher.students.show', compact('student', 'profile'));
    }

     /**
     * إزالة طالب من صف
     */
    public function removeStudent(EducationClass $class, User $student)
    {
        // 1) تأكد أن الصف ملك للمعلم الحالي
        if ($class->user_id !== auth()->id()) {
            abort(403, 'غير مخوّل لإدارة هذا الصف.');
        }

        // 2) تأكد أن الطالب فعلاً مرتبط بهذا الصف
        if (! $class->users()->where('user_id', $student->id)->exists()) {
            return back()->with('error', 'هذا الطالب غير مرتبط بهذا الصف.');
        }

        // 3) فصّل الطالب عن الصف
        $class->users()->detach($student->id);

        // 4) إعادة توجيه مع رسالة نجاح
        return back()->with('success', "تمت إزالة الطالب {$student->first_name} من الصف {$class->name}.");
    }

}
