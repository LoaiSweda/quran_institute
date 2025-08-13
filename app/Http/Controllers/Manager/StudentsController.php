<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentsController extends Controller
{
    /** معهد المدير الحالي */
    protected function currentInstitute(): Institute
    {
        return Institute::where('user_id', auth()->id())->firstOrFail();
    }

    /** استعلام مقيّد بطلاب المعهد الحالي عبر pivot */
    protected function studentsQueryForCurrentInstitute()
    {
        $inst = $this->currentInstitute();
        return Student::query()
            ->with(['user','guardian','classes.subject','exams','progress'])
            ->whereHas('user.institutes', function ($q) use ($inst) {
                $q->where('institute_id', $inst->id);
            });
    }

    /**
     * 1) قائمة الطلاب (مُقيّدة بمعهد المدير)
     */
    public function index(Request $request)
    {
        $inst = $this->currentInstitute();

        // مصادر الفلاتر داخل نفس المعهد فقط
        $subjects = Subject::where('institute_id', $inst->id)->get();
        $classes  = EducationClass::whereHas('subject', fn($q)=>$q->where('institute_id',$inst->id))->get();
        $levels   = Subject::where('institute_id', $inst->id)->pluck('level')->unique()->filter()->values();

        $query = $this->studentsQueryForCurrentInstitute()
            ->when($request->subject_id, function($q, $id) {
                return $q->whereHas('classes.subject', fn($q2)=>$q2->where('subjects.id', $id));
            })
            ->when($request->class_id, function($q, $id) {
                return $q->whereHas('classes', fn($q2)=>$q2->where('classes.id', $id));
            })
            ->when($request->level, function($q, $lvl) {
                return $q->whereHas('classes.subject', fn($q2)=>$q2->where('subjects.level', $lvl));
            })
            ->when($request->attendance_min, fn($q,$min)=>$q->where('present_percentage','>=',$min))
            ->when($request->min_score, function($q,$min){
                return $q->whereHas('exams', function($qe) use ($min) {
                    $qe->selectRaw('student_id, MAX(score) as max_score')
                        ->groupBy('student_id')
                        ->having('max_score','>=',$min);
                });
            })
            ->orderBy('last_name');

        $students = $query->paginate(25)->withQueryString();

        return view('manager.students.index', compact('students','subjects','classes','levels'));
    }

    /**
     * 2) نموذج إنشاء طالب (يُظهر الأوصياء والطلاب داخل المعهد الحالي فقط)
     */
    public function create()
    {
        $inst = $this->currentInstitute();

        $guardians = Guardian::whereHas('user.institutes', fn($q)=>$q->where('institute_id',$inst->id))
            ->with('user:id,email')->get();

        $students  = $this->studentsQueryForCurrentInstitute()
            ->orderBy('created_at','desc')
            ->paginate(10);

        return view('manager.students.create', compact('guardians','students'));
    }

    /**
     * 3) حفظ طالب جديد: users + students + ربط بمعهد المدير فقط
     */
    public function store(Request $request)
    {
        $inst = $this->currentInstitute();

        $data = $request->validate([
            'first_name'   => 'required|string|max:50',
            'last_name'    => 'required|string|max:50',
            'birthdate'    => 'nullable|date',
            'phone'        => 'nullable|string',
            'address'      => 'nullable|string',
            'father_name'  => 'nullable|string',
            'guardian_id'  => 'nullable|exists:guardians,id',
            'email'        => ['required','email','unique:users,email'],
            'password'     => 'required|string|min:6|confirmed',
        ]);

        // توليد QR فريد
        $qr = Str::upper(Str::random(8));
        while (Student::where('qr', $qr)->exists()) {
            $qr = Str::upper(Str::random(8));
        }

        $roleId = Role::where('name','student')->value('id');
        abort_if(!$roleId, 500, "Role 'student' not found.");

        DB::transaction(function () use ($data, $qr, $roleId, $inst) {

            // users
            $user = User::create([
                'name'     => "{$data['first_name']} {$data['last_name']}",
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id'  => $roleId,
            ]);

            // students
            $student = Student::create([
                'first_name'         => $data['first_name'],
                'last_name'          => $data['last_name'],
                'birthdate'          => $data['birthdate'] ?? null,
                'phone'              => $data['phone'] ?? null,
                'address'            => $data['address'] ?? null,
                'father_name'        => $data['father_name'] ?? null,
                'guardian_id'        => $data['guardian_id'] ?? null,
                'qr'                 => $qr,
                'user_id'            => $user->id,
                'points'             => 0,
                'present_percentage' => 0,
            ]);

            // ربط بمعهد المدير فقط (sync يحذف أي روابط أخرى)
            $user->institutes()->sync([
                $inst->id => ['role_institute' => 'student']
            ]);
            // تأكد أن علاقة institutes() في User فيها ->withTimestamps()
        });

        return redirect()->route('manager.students.index')
            ->with('success','تم إضافة الطالب وربطه بالمعهد الحالي فقط.');
    }

    /**
     * 4) عرض طالب (مع تحقق الانتماء للمعهد الحالي)
     */
    public function show(Student $student)
    {
        // منع الوصول لطلاب معاهد أخرى
        $allowed = $this->studentsQueryForCurrentInstitute()
            ->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        $student->load([
            'user','guardian',
            'classes.teacher','classes.sessionSchedules','classes.subject',
            'exams','progress',
        ]);

        return view('manager.students.show', compact('student'));
    }

    /**
     * 5) نموذج تعديل (مع تحقق الانتماء)
     */
    public function edit(Student $student)
    {
        $inst = $this->currentInstitute();

        $allowed = $this->studentsQueryForCurrentInstitute()
            ->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        $guardians = Guardian::whereHas('user.institutes', fn($q)=>$q->where('institute_id',$inst->id))
            ->get();

        return view('manager.students.edit', compact('student','guardians'));
    }

    /**
     * 6) حفظ التعديلات
     */
    public function update(Request $request, Student $student)
    {
        $allowed = $this->studentsQueryForCurrentInstitute()
            ->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        $data = $request->validate([
            'first_name'   => 'required|string|max:50',
            'last_name'    => 'required|string|max:50',
            'birthdate'    => 'nullable|date',
            'phone'        => 'nullable|string',
            'address'      => 'nullable|string',
            'father_name'  => 'nullable|string',
            'guardian_id'  => 'nullable|exists:guardians,id',
        ]);

        $student->update($data);

        return redirect()->route('manager.students.show', $student)
            ->with('success','تم تحديث بيانات الطالب.');
    }

    /**
     * 7) حذف الطالب (مع فك الربط من هذا المعهد فقط)
     */
    public function destroy(Student $student)
    {
        $inst = $this->currentInstitute();

        $allowed = $this->studentsQueryForCurrentInstitute()
            ->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        DB::transaction(function () use ($student, $inst) {
            // فك الربط من هذا المعهد فقط
            $student->user->institutes()->detach($inst->id);

            // حذف الطالب (اترك user حسب رغبتك)
            $student->forceDelete();
        });

        return redirect()->route('manager.students.index')
            ->with('success','تم حذف الطالب من هذا المعهد.');
    }
}
