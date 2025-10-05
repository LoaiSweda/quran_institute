<?php

namespace App\Http\Controllers\Manager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentsController extends Controller
{
    protected function currentInstitute(): Institute
    {
        return Institute::where('user_id', auth()->id())->firstOrFail();
    }

    protected function studentsQueryForCurrentInstitute()
    {
        $inst = $this->currentInstitute();
        return Student::query()
            ->with(['user','guardian','classes.subject','exams','progress'])
            ->whereHas('user.institutes', function ($q) use ($inst) {
                $q->where('institute_id', $inst->id);
            });
    }


    public function index(Request $request)
    {
        $inst = $this->currentInstitute();

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

    public function store(Request $request)
    {
        $inst = $this->currentInstitute();

        $data = $request->validate([
            'first_name'        => 'required|string|max:50',
            'last_name'         => 'required|string|max:50',
            'birthdate'         => 'nullable|date',
            'phone'             => 'nullable|string',
            'address'           => 'nullable|string',
            'father_name'       => 'nullable|string',
            'father_job'        => 'nullable|string|max:100',
            'mother_job'        => 'nullable|string|max:100',
            'school_name'       => 'nullable|string|max:100',
            'financial_status'  => 'nullable|in:ممتاز,متوسط,ضعيف',
            'health_status'     => 'nullable|string|max:500',
            'memorized_parts'   => 'nullable|integer|min:0|max:60',
            'has_sibling'       => 'nullable|boolean',
            'siblings_count'    => 'nullable|integer|min:0',
            'guardian_id'       => 'nullable|exists:guardians,id',
            'email'             => ['required','email','unique:users,email'],
            'password'          => 'required|string|min:6|confirmed',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $qr = Str::upper(Str::random(8));
        while (Student::where('qr', $qr)->exists()) {
            $qr = Str::upper(Str::random(8));
        }

        $roleId = Role::where('name','student')->value('id');
        abort_if(!$roleId, 500, "Role 'student' not found.");

        DB::transaction(function () use ($data, $qr, $roleId, $inst, $request) {
            $user = User::create([
                'name'     => "{$data['first_name']} {$data['last_name']}",
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id'  => $roleId,
            ]);

            // معالجة رفع الصورة
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('students', 'public');
            }

            $student = Student::create([
                'first_name'         => $data['first_name'],
                'last_name'          => $data['last_name'],
                'birthdate'          => $data['birthdate'] ?? null,
                'phone'              => $data['phone'] ?? null,
                'address'            => $data['address'] ?? null,
                'father_name'        => $data['father_name'] ?? null,
                'father_job'         => $data['father_job'] ?? null,
                'mother_job'         => $data['mother_job'] ?? null,
                'school_name'        => $data['school_name'] ?? null,
                'financial_status'   => $data['financial_status'] ?? null,
                'health_status'      => $data['health_status'] ?? null,
                'memorized_parts'    => $data['memorized_parts'] ?? 0,
                'has_sibling'        => $data['has_sibling'] ?? false,
                'siblings_count'     => $data['siblings_count'] ?? 0,
                'guardian_id'        => $data['guardian_id'] ?? null,
                'qr'                 => $qr,
                'user_id'            => $user->id,
                'points'             => 0,
                'present_percentage' => 0,
                'image'              => $imagePath,
            ]);

            $user->institutes()->sync([
                $inst->id => ['role_institute' => 'student']
            ]);
        });

        return redirect()->route('manager.students.index')
            ->with('success','تم إضافة الطالب وربطه بالمعهد الحالي فقط.');
    }

    public function show(Student $student)
    {
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

    public function update(Request $request, Student $student)
    {
        $allowed = $this->studentsQueryForCurrentInstitute()
            ->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        $data = $request->validate([
            'first_name'        => 'required|string|max:50',
            'last_name'         => 'required|string|max:50',
            'birthdate'         => 'nullable|date',
            'phone'             => 'nullable|string',
            'address'           => 'nullable|string',
            'father_name'       => 'nullable|string',
            'father_job'        => 'nullable|string|max:100',
            'mother_job'        => 'nullable|string|max:100',
            'school_name'       => 'nullable|string|max:100',
            'financial_status'  => 'nullable|in:ممتاز,متوسط,ضعيف',
            'health_status'     => 'nullable|string|max:500',
            'memorized_parts'   => 'nullable|integer|min:0|max:60',
            'has_sibling'       => 'nullable|boolean',
            'siblings_count'    => 'nullable|integer|min:0',
            'guardian_id'       => 'nullable|exists:guardians,id',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // معالجة رفع الصورة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($student->image) {
                Storage::disk('public')->delete($student->image);
            }
            $imagePath = $request->file('image')->store('students', 'public');
            $data['image'] = $imagePath;
        } else {
            unset($data['image']);
        }

        $student->update($data);

        return redirect()->route('manager.students.show', $student)
            ->with('success','تم تحديث بيانات الطالب.');
    }    public function destroy(Student $student)
    {
        $inst = $this->currentInstitute();

        $allowed = $this->studentsQueryForCurrentInstitute()
            ->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        DB::transaction(function () use ($student, $inst) {
            $student->user->institutes()->detach($inst->id);

            $student->forceDelete();
        });

        return redirect()->route('manager.students.index')
            ->with('success','تم حذف الطالب من هذا المعهد.');
    }
}
