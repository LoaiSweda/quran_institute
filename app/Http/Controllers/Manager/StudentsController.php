<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Role;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function Illuminate\Database\Query\orderBy;

class StudentsController extends Controller
{
    /**
     * عرض قائمة الطلاب مع الإحصائيات
     */
    public function index(Request $request)
    {
        $subjects = Subject::all();
        $classes  = EducationClass::all();
        $levels   = Subject::pluck('level')->unique()->filter()->values();

        $query = Student::with(['user','guardian','classes.subject','exams','progress'])
            ->when($request->subject_id, function($q, $id) {
                return $q->whereHas('classes.subject', function($q2) use ($id) {
                    $q2->where('subjects.id', $id);
                });
            })
            ->when($request->class_id, function($q, $id) {
                return $q->whereHas('classes', function($q2) use ($id) {
                    $q2->where('classes.id', $id);
                });
            })
            ->when($request->level, function($q, $lvl) {
                return $q->whereHas('classes.subject', function($q2) use ($lvl) {
                    $q2->where('subjects.level', $lvl);
                });
            })
            ->when($request->attendance_min, function($q, $min) {
                return $q->where('present_percentage', '>=', $min);
            })
            ->when($request->min_score, function($q, $min) {
                return $q->whereHas('exams', function($qe) use ($min) {
                    $qe->selectRaw('student_id, MAX(score) as max_score')
                        ->groupBy('student_id')
                        ->having('max_score', '>=', $min);
                });
            })
            ->orderBy('last_name');

        $students = $query->paginate(25)->withQueryString();

        return view('manager.students.index', compact(
            'students','subjects','classes','levels'
        ));
    }



    /**
     * إظهار نموذج إنشاء طالب (الحقول الأساسية فقط)
     */
    public function create()
    {
        $guardianRoleId = 6;

        $guardians = Guardian::whereHas('user', function($q) use($guardianRoleId) {
            $q->where('role_id', $guardianRoleId);
        })->get();

        $students  = Student::with('user')->orderBy('created_at','desc')->paginate(10);

        return view('manager.students.create', compact('guardians','students'));
    }

    /**
     * حفظ طالب جديد مع توليد QR تلقائي
     */

    public function store(Request $request)
    {
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

        $qr = Str::upper(Str::random(8));
        while (Student::where('qr', $qr)->exists()) {
            $qr = Str::upper(Str::random(8));
        }

        $roleId = Role::where('name','student')->value('id');
        if (! $roleId) {
            abort(500, "Role 'student' not found.");
        }

        $user = User::create([
            'name'     => "{$data['first_name']} {$data['last_name']}",
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $roleId,
        ]);

        $student = Student::create([
            'first_name'        => $data['first_name'],
            'last_name'         => $data['last_name'],
            'birthdate'         => $data['birthdate'],
            'phone'             => $data['phone'],
            'address'           => $data['address'],
            'father_name'       => $data['father_name'],
            'guardian_id'       => $data['guardian_id'],
            'qr'                => $qr,
            'user_id'           => $user->id,
            'points'            => 0,
            'present_percentage'=> 0,
        ]);

        $institute = auth()->user()->institute;

        if ($institute) {
            $institute->users()->attach($user->id, [
                'role_institute' => 'student',
            ]);
        }

        return redirect()
            ->route('manager.students.index')
            ->with('success','تم إضافة الطالب وربطه بالمعهد بنجاح.');
    }

    /**
     * عرض تفاصيل طالب مع إحصائياته
     */
    public function show(Student $student)
    {
        $student->load([
            'user',
            'guardian',
            'classes.teacher',
            'classes.sessionSchedules',

            'classes.subject',
            'exams',
            'progress',
        ]);

        return view('manager.students.show', compact('student'));
    }

    /**
     * إظهار نموذج تعديل بيانات الطالب (الحقول الأساسية فقط)
     */
    public function edit(Student $student)
    {
        $guardianRoleId = 6;

        $guardians = Guardian::whereHas('user', function($q) use($guardianRoleId) {
            $q->where('role_id', $guardianRoleId);
        })->get();

        return view('manager.students.edit', compact('student','guardians'));
    }
    /**
     * حفظ تعديل بيانات الطالب
     */
    public function update(Request $request, Student $student)
    {
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

        return redirect()
            ->route('manager.students.show', $student)
            ->with('success','تم تحديث بيانات الطالب.');
    }

    /**
     * حذف الطالب
     */
    public function destroy(Student $student)
    {
        $student->forceDelete();
        return redirect()
            ->route('manager.students.index')
            ->with('success','تم حذف الطالب.');
    }
}
