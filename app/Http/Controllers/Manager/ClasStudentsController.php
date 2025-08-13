<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClasStudentsController extends Controller
{
    protected function currentInstitute()
    {

        return Institute::where('user_id', auth()->id())
            ->firstOrFail();
    }


    public function index(EducationClass $class)
    {

        $class->load('users.studentProfile');
        $students = $class->users()->where('users.role_id', '<>', 4)->get();

        return view('manager.classes.students.index', compact('class','students'));
    }


    public function create(EducationClass $class)
    {
        $inst = $this->currentInstitute();

        // 1) الطلاب الموجودون بالفعل في هذه الحلقة
        $added = $class->users()->pluck('users.id')->toArray();

        // 2) جلب معلومات المادة المرتبطة بالحلقة الحالية
        $subject = $class->subject; // يفترض وجود علاقة subject() في EducationClass

        $enrolledInSubject = [];
        if ($subject && $subject->is_active) {
            // 3) إذا كانت المادة مفعّلة: اجمع كل الحلقات لنفس المادة ثم الطلاب المسجلين فيها
            $otherClassIds = EducationClass::where('subject_id', $class->subject_id)
                ->pluck('id')
                ->toArray();

            if (!empty($otherClassIds)) {
                $enrolledInSubject = DB::table('users_classes')
                    ->whereIn('class_id', $otherClassIds)
                    ->pluck('user_id')
                    ->toArray();
            }
        }
        // إذا كانت المادة غير مفعّلة، نترك $enrolledInSubject فارغة (لا نستثني أحداً بناءً على المادة)

        // 4) استبعاد الجمعتين معاً
        $exclude = array_unique(array_merge($added, $enrolledInSubject));

        // 5) جلب طلاب المعهد الذين ليسوا في $exclude
        //    نستخدم علاقة users() على Institute لأن pivot موجود هناك
        //    ونضم جدول students لاستخراج first_name/last_name (أو غيّرها إن أسماء الطلبة في جدول آخر)
        $studentsQuery = $inst->users()
            ->wherePivot('role_institute', 'student')
            ->whereNotIn('users.id', $exclude)
            ->join('students', 'students.user_id', '=', 'users.id') // غيّر 'students' إذا لديك جدول آخر
            ->select([
                'users.id',
                'users.email',
                'students.first_name',
                'students.last_name',
            ])
            ->orderBy('students.first_name');

        // لو كنت تتوقّع عددًا كبيرًا من النتائج يمكنك هنا استخدام pagination:
         $students = $studentsQuery->paginate(30);
        $students = $studentsQuery->get();

        return view('manager.classes.students.create', compact('class','students'));
    }


    public function store(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute();

        $data = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        DB::table('institute_user')->updateOrInsert(
            ['institute_id'=>$inst->id,'user_id'=>$data['user_id']],
            ['role_institute'=>'student']
        );

        $class->users()->attach($data['user_id']);

        return redirect()
            ->route('manager.classes.students.index', $class)
            ->with('success','تم إضافة الطالب إلى الحلقة');
    }

    public function destroy(EducationClass $class, User $user)
    {
        $class->users()->detach($user->id);

        return back()->with('success','تم إزالة الطالب من الحلقة');
    }
}
