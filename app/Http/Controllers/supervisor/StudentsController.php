<?php


namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Guardian;
use App\Models\Institute;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentsController extends Controller
{
    /**
     * جلب معاهد المشرف (IDs) من pivot institute_user.
     */
    protected function myInstituteIds(): array
    {
        return auth()->user()
            ? auth()->user()->institutes()->pluck('institutes.id')->toArray()
            : [];
    }

    /**
     * تحديد المعهد الحالي للمشرف:
     * - إن كان مرتبطًا بمعهد واحد: يرجع هذا المعهد.
     * - إن كان مرتبطًا بعدة معاهد: يأخذ ?institute_id= ويتأكد أنه ضمن معاهده.
     */
    protected function currentInstitute(Request $request): Institute
    {
        $ids = $this->myInstituteIds();
        abort_if(empty($ids), 403, 'لا تملك صلاحية على أي معهد.');

        if (count($ids) === 1) {
            return Institute::findOrFail($ids[0]);
        }

        $iid = (int)$request->input('institute_id');
        abort_if(!$iid, 422, 'يرجى تحديد المعهد عبر المعامل ?institute_id=');
        abort_if(!in_array($iid, $ids, true), 403, 'المعهد المحدد غير مصرح به.');
        return Institute::findOrFail($iid);
    }

    /**
     * قيّد استعلام الطلاب بمعاهد المشرف.
     */
    protected function studentsQueryRestrictedToMe()
    {
        $ids = $this->myInstituteIds();
        abort_if(empty($ids), 403, 'لا تملك صلاحية على أي معهد.');

        // الطلاب الذين user_id لهم مرتبط بأحد معاهد المشرف في جدول institute_user
        return Student::query()
            ->with(['user', 'guardian', 'classes.subject', 'exams', 'progress'])
            ->whereHas('user.institutes', function ($q) use ($ids) {
                $q->whereIn('institute_id', $ids);
            });
    }

    /**
     * 1) عرض قائمة الطلاب مع الفلاتر (مقيدة بمعاهد المشرف)
     */
    public function index(Request $request)
    {
        $subjects = Subject::query()
            ->whereIn('institute_id', $this->myInstituteIds())
            ->get();

        $classes = EducationClass::whereHas('subject', function ($q) {
            $q->whereIn('institute_id', $this->myInstituteIds());
        })->get();

        $levels = Subject::whereIn('institute_id', $this->myInstituteIds())
            ->pluck('level')->unique()->filter()->values();

        $query = $this->studentsQueryRestrictedToMe()
            ->when($request->subject_id, function ($q, $id) {
                return $q->whereHas('classes.subject', function ($q2) use ($id) {
                    $q2->where('subjects.id', $id);
                });
            })
            ->when($request->class_id, function ($q, $id) {
                return $q->whereHas('classes', function ($q2) use ($id) {
                    $q2->where('classes.id', $id);
                });
            })
            ->when($request->level, function ($q, $lvl) {
                return $q->whereHas('classes.subject', function ($q2) use ($lvl) {
                    $q2->where('subjects.level', $lvl);
                });
            })
            ->when($request->attendance_min, function ($q, $min) {
                return $q->where('present_percentage', '>=', $min);
            })
            ->when($request->min_score, function ($q, $min) {
                return $q->whereHas('exams', function ($qe) use ($min) {
                    $qe->selectRaw('student_id, MAX(score) as max_score')
                        ->groupBy('student_id')
                        ->having('max_score', '>=', $min);
                });
            })
            ->orderBy('last_name');

        $students = $query->paginate(25)->withQueryString();

        return view('supervisor.students.index', compact(
            'students', 'subjects', 'classes', 'levels'
        ));
    }

    /**
     * 2) نموذج إنشاء طالب جديد
     * يعرض قائمة أولياء الأمور فقط ضمن معاهد المشرف.
     * ويعرض جدولًا صغيرًا بآخر طلاب المعهد المحدد أسفل الصفحة (اختياري).
     */
    public function create(Request $request)
    {
        // معهد المشرف الحالي (منطقك الموجود)
        $inst = $this->currentInstitute($request);

        // id دور الوصي
        $guardianRoleId = \App\Models\Role::where('name', 'guardian')->value('id');

        // الأوصياء ضمن هذا المعهد فقط + فلترة على role_id في users
        $guardians = \App\Models\Guardian::with('user:id,email')
            ->when($guardianRoleId, function ($q) use ($guardianRoleId) {
                $q->whereHas('user', function ($uq) use ($guardianRoleId) {
                    $uq->where('role_id', $guardianRoleId);  // <-- بدلاً من user.roles
                });
            })
            ->whereHas('user.institutes', function ($iq) use ($inst) {
                $iq->where('institute_id', $inst->id);
            })
            ->orderBy('firstname')   // <-- أعمدة guardians الفعلية
            ->orderBy('lastname')
            ->get();

        // آخر الطلاب في نفس المعهد
        $students  = \App\Models\Student::with('user')
            ->whereHas('user.institutes', fn($q) => $q->where('institute_id', $inst->id))
            ->orderBy('created_at','desc')
            ->paginate(10)
            ->withQueryString();

        return view('supervisor.students.create', compact('guardians','students','inst'));
    }


    /**
     * 3) حفظ طالب جديد: users + students + institute_user
     */
    public function store(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'birthdate' => 'nullable|date',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'father_name' => 'nullable|string',
            'guardian_id' => 'nullable|exists:guardians,id',
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => 'required|string|min:6|confirmed',
        ]);

        // توليد QR فريد
        $qr = Str::upper(Str::random(8));
        while (Student::where('qr', $qr)->exists()) {
            $qr = Str::upper(Str::random(8));
        }

        $roleId = Role::where('name', 'student')->value('id');
        abort_if(!$roleId, 500, "Role 'student' not found.");

        DB::transaction(function () use ($data, $qr, $roleId, $inst) {

            // users
            $user = User::create([
                'name' => "{$data['first_name']} {$data['last_name']}",
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $roleId,
            ]);

            // students
            $student = Student::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'birthdate' => $data['birthdate'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'father_name' => $data['father_name'] ?? null,
                'guardian_id' => $data['guardian_id'] ?? null,
                'qr' => $qr,
                'user_id' => $user->id,
                'points' => 0,
                'present_percentage' => 0,
            ]);

            // institute_user (role_institute + timestamps)
            $user->institutes()->attach($inst->id, [
                'role_institute' => 'student',
            ]);
        });

        return redirect()
            ->route('admin.students.index', ['institute_id' => $inst->id])
            ->with('success', 'تم إضافة الطالب وربطه بالمعهد بنجاح.');
    }

    /**
     * 4) عرض طالب
     */
    public function show(Request $request, Student $student)
    {
        // تحقق أن الطالب ضمن معاهد المشرف
        $allowed = $this->studentsQueryRestrictedToMe()->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        $student->load([
            'user', 'guardian',
            'classes.teacher', 'classes.sessionSchedules', 'classes.subject',
            'exams', 'progress',
        ]);

        return view('supervisor.students.show', compact('student'));
    }

    /**
     * 5) نموذج تعديل طالب
     */
    public function edit(Request $request, Student $student)
    {
        // تحقق الصلاحية
        $allowed = $this->studentsQueryRestrictedToMe()->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        $inst = $this->currentInstitute($request);

        $guardians = Guardian::whereHas('user.institutes', function ($q) use ($inst) {
            $q->where('institute_id', $inst->id);
        })->get();

        return view('supervisor.students.edit', compact('student', 'guardians', 'inst'));
    }

    /**
     * 6) حفظ تعديل طالب
     */
    public function update(Request $request, Student $student)
    {
        // تحقق الصلاحية
        $allowed = $this->studentsQueryRestrictedToMe()->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'birthdate' => 'nullable|date',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'father_name' => 'nullable|string',
            'guardian_id' => 'nullable|exists:guardians,id',
        ]);

        $student->update($data);

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', 'تم تحديث بيانات الطالب.');
    }

    /**
     * 7) حذف طالب (نهائي)
     */
    public function destroy(Student $student)
    {
        $allowed = $this->studentsQueryRestrictedToMe()->where('students.id', $student->id)->exists();
        abort_if(!$allowed, 403, 'لا تملك صلاحية على هذا الطالب.');

        // حذف نهائي (إن أردت soft delete غيّرها)
        $student->forceDelete();

        // ملاحظة: إن رغبت حذف user وربطه من institute_user كذلك، أضِف ذلك هنا بحذر.

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'تم حذف الطالب.');
    }
}

