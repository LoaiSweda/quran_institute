<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;         // ← تأكد من هذا
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClasStudentsController extends Controller
{
    protected function currentInstitute()
    {
        // جلب المعهد الذي يديره المستخدم الحالي عبر عمود user_id
        return Institute::where('user_id', auth()->id())
            ->firstOrFail();
    }


    // عرض الطلاب الحاليين في الحلقة
    public function index(EducationClass $class)
    {
        // نحمّل users وعلاقتهم مع studentProfile دفعة واحدة
        $class->load('users.studentProfile');
        $students = $class->users()->where('users.role_id', '<>', 4)->get();

        return view('manager.classes.students.index', compact('class','students'));
    }

    // نموذج لإضافة طالب
//    public function create(EducationClass $class)
//    {
//
//        $inst = $this->currentInstitute();
//
//        // جلب كل طلاب المعهد غير المضافين للحلقة بعد
//        $added = $class->users()->pluck('users.id')->toArray();
//
//        $students = DB::table('institute_user')
//            ->join('users','institute_user.user_id','=','users.id')
//            ->where('institute_user.institute_id', $inst->id)
//            ->where('institute_user.role_institute', 'student')
//            ->whereNotIn('users.id', $added)
//            ->select('users.id','users.email') // أو مع الانضمام لجدول teachers لfirstname/lastname
//            ->get();
//
//        return view('manager.classes.students.create', compact('class','students'));
//    }

//    public function create(EducationClass $class)
//    {
//        $inst = $this->currentInstitute();
//
//        // 1) الطلاب الذين أُضيفوا فعلاً لهذه الحلقة
//        $added = $class->users()->pluck('users.id')->toArray();
//
//        // 2) الحلقات الأخرى من نفس المادة
//        $otherClassIds = EducationClass::where('subject_id', $class->subject_id)
//            ->pluck('id')
//            ->toArray();
//
//        // 3) الطلاب المسجلين في أي حلقة من نفس المادة
//        $enrolledInSubject = DB::table('users_classes')
//            ->whereIn('class_id', $otherClassIds)
//            ->pluck('user_id')
//            ->toArray();
//
//        // 4) استثناء المجموعتين معاً
//        $exclude = array_unique(array_merge($added, $enrolledInSubject));
//
//        // 5) جلب طلاب المعهد (institute_user.role_institute = 'student')
//        //    مع بيانات الأسماء من جدول teachers
//        $students = DB::table('institute_user')
//            ->join('users',        'institute_user.user_id', '=', 'users.id')
//            ->join('teachers',     'users.id',               '=', 'teachers.user_id')
//            ->where('institute_user.institute_id',  $inst->id)
//            ->where('institute_user.role_institute','student')
//            ->whereNotIn('users.id', $exclude)
//            ->select([
//                'users.id',
//                'teachers.first_name',
//                'teachers.last_name',
//                'users.email',
//            ])
//            ->get();
//
//        return view('manager.classes.students.create', compact('class','students'));
//    }


// في ClasStudentsController.php

    public function create(EducationClass $class)
    {
        $inst = $this->currentInstitute();

        // 1) كل حلقات نفس المادة
        $otherClassIds = EducationClass::where('subject_id', $class->subject_id)
            ->pluck('id')
            ->toArray();

        // 2) الطلاب المسجّلين في أي من هذه الحلقات
        $enrolledInSubject = DB::table('users_classes')
            ->whereIn('class_id', $otherClassIds)
            ->pluck('user_id')
            ->toArray();

        // 3) جلب طلاب المعهد (role 'student') الذين لم يُسجَّلوا بعد
        $students = $inst->users()                    // users المرتبطون بالمعهد
        ->wherePivot('role_institute', 'student') // دورهم “student”
        ->whereNotIn('users.id', $enrolledInSubject)
            // انضمام إلى جدول students للحصول على first_name/last_name
            ->join('students', 'students.user_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.email',
                'students.first_name',
                'students.last_name',
            ])
            ->get();

        return view('manager.classes.students.create', compact('class','students'));
    }


    // حفظ الربط (pivot) في institute_user و users_classes
    public function store(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute();

        $data = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // 1) تأكد من وجود الطالب في institute_user (role student)
        DB::table('institute_user')->updateOrInsert(
            ['institute_id'=>$inst->id,'user_id'=>$data['user_id']],
            ['role_institute'=>'student']
        );

        // 2) ثم اربطه بالحلقة
        $class->users()->attach($data['user_id']);

        return redirect()
            ->route('manager.classes.students.index', $class)
            ->with('success','تم إضافة الطالب إلى الحلقة');
    }

    // إزالة الطالب من الحلقة فقط
    public function destroy(EducationClass $class, User $user)
    {
        $class->users()->detach($user->id);

        return back()->with('success','تم إزالة الطالب من الحلقة');
    }
}
