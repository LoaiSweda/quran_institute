<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClasStudentsController extends Controller
{
    /**
     * IDs المعاهد المرتبط بها المشرف عبر pivot institute_user
     */
    protected function myInstituteIds(): array
    {
        return auth()->user()
            ? auth()->user()->institutes()->pluck('institutes.id')->toArray()
            : [];
    }

    /**
     * تحديد المعهد الحالي للمشرف (admin)
     * - إن كان معهدًا واحدًا: يرجع نفسه
     * - إن كانت عدّة: يعتمد على ?institute_id= بعد التحقق
     */
    protected function currentInstitute(Request $request): Institute
    {
        $ids = $this->myInstituteIds();
        abort_if(empty($ids), 403, 'لا تملك صلاحية على أي معهد.');

        if (count($ids) === 1) {
            return Institute::findOrFail($ids[0]);
        }

        $iid = (int) $request->query('institute_id');
        abort_if(!$iid, 422, 'حدد المعهد عبر ?institute_id=');
        abort_if(!in_array($iid, $ids, true), 403, 'هذا المعهد غير مرتبط بحسابك.');
        return Institute::findOrFail($iid);
    }

    /**
     * تحقق أن الحلقة تتبع المعهد الحالي
     */
    protected function assertClassInInstitute(EducationClass $class, Institute $inst): void
    {
        $class->loadMissing('subject:id,institute_id');
        abort_if(
            !$class->subject || $class->subject->institute_id !== $inst->id,
            403,
            'لا تملك صلاحية على هذه الحلقة.'
        );
    }

    public function index(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        $class->load('users.studentProfile');
        $students = $class->users()->where('users.role_id', '<>', 4)->get();

        return view('supervisor.classes.students.index', compact('class','students','inst'));
    }

    public function create(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        // 1) الطلاب الموجودون بالفعل في هذه الحلقة
        $added = $class->users()->pluck('users.id')->toArray();

        // 2) جلب معلومات المادة المرتبطة بالحلقة
        $subject = $class->subject;

        // 3) إن كانت المادة مفعّلة: استثناء الطلاب المسجلين في أي حلقة أخرى لنفس المادة
        $enrolledInSubject = [];
        if ($subject && $subject->is_active) {
            $otherClassIds = EducationClass::where('subject_id', $class->subject_id)->pluck('id')->toArray();
            if (!empty($otherClassIds)) {
                $enrolledInSubject = DB::table('users_classes')
                    ->whereIn('class_id', $otherClassIds)
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        // 4) الاستثناء النهائي
        $exclude = array_unique(array_merge($added, $enrolledInSubject));

        // 5) طلاب نفس المعهد وغير مستثنين
        $students = $inst->users()
            ->wherePivot('role_institute', 'student')
            ->whereNotIn('users.id', $exclude)
            ->join('students', 'students.user_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.email',
                'students.first_name',
                'students.last_name',
            ])
            ->orderBy('students.first_name')
            ->get();

        return view('supervisor.classes.students.create', compact('class','students','inst'));
    }

    public function store(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // تأكيد ربط الطالب بالمعهد كـ student
        DB::table('institute_user')->updateOrInsert(
            ['institute_id' => $inst->id, 'user_id' => $data['user_id']],
            ['role_institute' => 'student', 'updated_at'=>now(), 'created_at'=>now()]
        );

        // ربط الطالب بالحلقة
        $class->users()->syncWithoutDetaching([$data['user_id']]);

        return redirect()
            ->route('admin.classes.students.index', ['class'=>$class->id, 'institute_id'=>$inst->id])
            ->with('success','تم إضافة الطالب إلى الحلقة');
    }

    public function destroy(Request $request, EducationClass $class, User $user)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        $class->users()->detach($user->id);

        return back()->with('success','تم إزالة الطالب من الحلقة');
    }
}
