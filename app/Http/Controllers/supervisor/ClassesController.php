<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassesController extends Controller
{
    /**
     * IDs المعاهد المرتبط بها المشرف عبر institute_user
     */
    protected function myInstituteIds(): array
    {
        return auth()->user()
            ? auth()->user()->institutes()->pluck('institutes.id')->toArray()
            : [];
    }

    /**
     * تحديد المعهد الحالي للمشرف (admin):
     * - إن كان مرتبطًا بمعهد واحد: يرجع نفسه
     * - إن كان مرتبطًا بأكثر من معهد: يعتمد على ?institute_id= بعد التحقق أنه ضمن معاهده
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
        abort_if(!in_array($iid, $ids, true), 403, 'هذا المعهد غير مرتبط بك.');
        return Institute::findOrFail($iid);
    }

    /**
     * قائمة الحلقات ضمن معهد المشرف الحالي فقط
     */
    public function index(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $classes = EducationClass::whereHas('subject', function ($q) use ($inst) {
            $q->where('institute_id', $inst->id);
        })
            ->with(['subject','teacher'])
            ->paginate(20)
            ->withQueryString();

        return view('supervisor.classes.index', compact('classes', 'inst'));
    }

    /**
     * نموذج إنشاء حلقة جديدة (ضمن معهد المشرف الحالي فقط)
     */
    public function create(Request $request)
    {
        $inst = $this->currentInstitute($request);

        // مواد نفس المعهد
        $subjects = Subject::where('institute_id', $inst->id)->get();

        // المعلمون المرتبطون بهذا المعهد عبر pivot institute_user (role_institute=teacher)
        $teachers = DB::table('institute_user')
            ->join('teachers', 'institute_user.user_id', '=', 'teachers.user_id')
            ->where('institute_user.institute_id', $inst->id)
            ->where('institute_user.role_institute', 'teacher')
            ->select([
                'teachers.user_id as id',
                'teachers.first_name',
                'teachers.last_name',
            ])
            ->get();

        // عرض بعض الحلقات الحالية لنفس المعهد (اختياري)
        $classes = EducationClass::whereHas('subject', function ($q) use ($inst) {
            $q->where('institute_id', $inst->id);
        })
            ->with(['subject', 'teacher'])
            ->paginate(10);

        return view('supervisor.classes.create', compact('subjects', 'teachers', 'classes', 'inst'));
    }

    /**
     * حفظ حلقة جديدة ضمن معهد المشرف الحالي فقط
     */
    public function store(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $data = $request->validate([
            'name'                     => 'required|string|max:255',
            'subject_id'               => 'required|exists:subjects,id',
            'user_id'                  => 'required|exists:users,id',
            'students_count'           => 'required|integer|min:1',
            'schedules'                => 'nullable|array',
            'schedules.*.day_of_week'  => 'required_with:schedules|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'schedules.*.start_time'   => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time'     => 'required_with:schedules|date_format:H:i|after:schedules.*.start_time',
        ]);

        // تحقق صارم: المادة المختارة ضمن هذا المعهد فقط
        abort_if(
            !Subject::where('id', $data['subject_id'])->where('institute_id', $inst->id)->exists(),
            403,
            'هذه المادة لا تتبع معهدك.'
        );

        // تحقق أن المعلّم مرتبط بنفس المعهد (وإلا اربطه)
        $teacherUserId = (int) $data['user_id'];

        DB::transaction(function () use ($data, $inst, $teacherUserId, &$cls) {
            // إنشاء الحلقة
            $cls = EducationClass::create([
                'name'           => $data['name'],
                'subject_id'     => $data['subject_id'],
                'user_id'        => $teacherUserId,   // معلم الحلقة
                'students_count' => $data['students_count'],
            ]);

            // تأكيد ربط المعلّم بالمعهد
            DB::table('institute_user')->updateOrInsert(
                [
                    'institute_id' => $inst->id,
                    'user_id'      => $teacherUserId,
                ],
                [
                    'role_institute' => 'teacher',
                    'updated_at'     => now(),
                    'created_at'     => now(),
                ]
            );

            // ربط المعلّم بالحصة (جدول users_classes)
            $cls->users()->syncWithoutDetaching([$teacherUserId]);

            // إنشاء الجداول الزمنية إن وجدت
            if (!empty($data['schedules'])) {
                foreach ($data['schedules'] as $sch) {
                    $cls->sessionSchedules()->create($sch);
                }
            }
        });

        return redirect()
            ->route('admin.classes.index', ['institute_id' => $inst->id])
            ->with('success', 'تم إنشاء الحلقة ضمن معهدك بنجاح.');
    }

    /**
     * عرض تفاصيل حلقة (بعد التحقق أنها داخل معهد المشرف الحالي)
     */
    public function show(Request $request, \App\Models\EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        abort_if($class->subject->institute_id !== $inst->id, 403, 'لا تملك صلاحية على هذه الحلقة.');

        $class->load([
            'subject',
            'teacher',
            'sessions',
            'exams.student',
            'progress',
            'enrolledStudents.student',
        ]);

        $students           = $class->enrolledStudents->pluck('student');
        $studentsCount      = $students->count();
        $presentPercentage  = $class->present_percentage ?? 0;
        $sessionsHeld       = $class->sessions->count();
        $totalPoints        = $class->exams->sum('points');

        return view('supervisor.classes.show', compact(
            'class','students','studentsCount','presentPercentage','sessionsHeld','totalPoints','inst'
        ));
    }

    /**
     * نموذج تعديل حلقة
     */
    public function edit(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        abort_if($class->subject->institute_id !== $inst->id, 403, 'لا تملك صلاحية على هذه الحلقة.');

        $class->load(['subject', 'teacher', 'sessionSchedules']);

        $subjects = Subject::where('institute_id', $inst->id)->get();

        $teachers = DB::table('institute_user')
            ->join('teachers', 'institute_user.user_id', '=', 'teachers.user_id')
            ->where('institute_user.institute_id', $inst->id)
            ->where('institute_user.role_institute', 'teacher')
            ->select([
                'teachers.user_id as id',
                'teachers.first_name',
                'teachers.last_name',
            ])
            ->get();

        return view('supervisor.classes.edit', compact('class','subjects','teachers','inst'));
    }

    /**
     * حفظ التعديلات
     */
    public function update(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        abort_if($class->subject->institute_id !== $inst->id, 403, 'لا تملك صلاحية على هذه الحلقة.');

        $data = $request->validate([
            'name'                     => 'required|string|max:255',
            'subject_id'               => 'required|exists:subjects,id',
            'user_id'                  => 'required|exists:users,id',
            'students_count'           => 'required|integer|min:1',
            'schedules'                => 'nullable|array',
            'schedules.*.id'           => 'nullable|integer',
            'schedules.*.day_of_week'  => 'required_with:schedules|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'schedules.*.start_time'   => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time'     => 'required_with:schedules|date_format:H:i|after:schedules.*.start_time',
        ]);

        // المادة يجب أن تكون لنفس المعهد
        abort_if(
            !Subject::where('id', $data['subject_id'])->where('institute_id', $inst->id)->exists(),
            403,
            'هذه المادة لا تتبع معهدك.'
        );

        $teacherUserId = (int) $data['user_id'];

        DB::transaction(function () use ($class, $data, $inst, $teacherUserId) {
            $class->update([
                'name'           => $data['name'],
                'subject_id'     => $data['subject_id'],
                'user_id'        => $teacherUserId,
                'students_count' => $data['students_count'],
            ]);

            // تأكيد ربط المعلّم بالمعهد
            DB::table('institute_user')->updateOrInsert(
                [
                    'institute_id' => $inst->id,
                    'user_id'      => $teacherUserId,
                ],
                [
                    'role_institute' => 'teacher',
                    'updated_at'     => now(),
                    'created_at'     => now(),
                ]
            );

            // ربط المعلّم بالحصة
            $class->users()->syncWithoutDetaching([$teacherUserId]);

            // مزامنة الجداول الزمنية: تحديث/إضافة/حذف
            $keep = [];
            if (!empty($data['schedules'])) {
                foreach ($data['schedules'] as $sch) {
                    if (!empty($sch['id'])) {
                        $schedule = $class->sessionSchedules()->findOrFail($sch['id']);
                        $schedule->update($sch);
                        $keep[] = (int) $sch['id'];
                    } else {
                        $new = $class->sessionSchedules()->create($sch);
                        $keep[] = $new->id;
                    }
                }
            }

            $class->sessionSchedules()
                ->when(!empty($keep), fn($q) => $q->whereNotIn('id', $keep))
                ->when(empty($keep), fn($q) => $q) // لا شيء للإبقاء عليه => حذف الكل
                ->delete();
        });

        return redirect()
            ->route('admin.classes.index', ['institute_id' => $inst->id])
            ->with('success', 'تم حفظ التعديلات على الحلقة.');
    }

    /**
     * حذف حلقة (ضمن صلاحية معهد المشرف)
     */
    public function destroy(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        abort_if($class->subject->institute_id !== $inst->id, 403, 'لا تملك صلاحية على هذه الحلقة.');

        $class->delete();

        return redirect()
            ->route('admin.classes.index', ['institute_id' => $inst->id])
            ->with('success', 'تم حذف الحلقة.');
    }
}
