<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\SessionSchedule;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassSchedulesController extends Controller
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
     * تحديد المعهد الحالي:
     * - إن كان المشرف مرتبطًا بمعهد واحد => يرجع نفسه
     * - إن كان مرتبطًا بأكثر من معهد => يعتمد على ?institute_id= بعد التحقق
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
     * تحقّق أن الحلقة تتبع المعهد الحالي
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

    /**
     * نموذج إنشاء موعد جديد للحلقة
     */
    public function create(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        $class->load(['sessionSchedules' => function($q){
            $q->orderBy('day_of_week')->orderBy('start_time');
        }]);

        // مسار العرض للمشرف
        return view('supervisor.classes.schedules.create', compact('class', 'inst'));
    }

    /**
     * حفظ موعد جديد
     */
    public function store(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        $data = $request->validate([
            'day_of_week' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
        ]);

        // نعين معلّم الحلقة (user_id) كصاحب الموعد
        $teacherUserId = $class->user_id;
        abort_if(!$teacherUserId, 422, 'لم يتم تعيين معلّم لهذه الحلقة.');

        $class->sessionSchedules()->create(array_merge($data, [
            'user_id' => $teacherUserId,
        ]));

        return redirect()
            ->route('admin.classes.edit', ['class' => $class->id, 'institute_id' => $inst->id])
            ->with('success', 'تم إضافة الموعد بنجاح.');
    }

    /**
     * نموذج تعديل موعد
     */
    public function edit(Request $request, EducationClass $class, SessionSchedule $schedule)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        abort_if($schedule->class_id !== $class->id, 403, 'الموعد لا يتبع هذه الحلقة.');

        $class->load(['sessionSchedules' => function($q){
            $q->orderBy('day_of_week')->orderBy('start_time');
        }]);

        return view('supervisor.classes.schedules.edit', compact('class', 'schedule', 'inst'));
    }

    /**
     * حفظ تعديل موعد
     */
    public function update(Request $request, EducationClass $class, SessionSchedule $schedule)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        abort_if($schedule->class_id !== $class->id, 403, 'الموعد لا يتبع هذه الحلقة.');

        $data = $request->validate([
            'day_of_week' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
        ]);

        // اختيارياً: مواكبة أي تغيير على معلّم الحلقة
        if ($class->user_id) {
            $data['user_id'] = $class->user_id;
        }

        $schedule->update($data);

        return redirect()
            ->route('admin.classes.edit', ['class' => $class->id, 'institute_id' => $inst->id])
            ->with('success', 'تم تحديث الموعد بنجاح.');
    }

    /**
     * حذف موعد
     */
    public function destroy(Request $request, EducationClass $class, SessionSchedule $schedule)
    {
        $inst = $this->currentInstitute($request);
        $this->assertClassInInstitute($class, $inst);

        abort_if($schedule->class_id !== $class->id, 403, 'الموعد لا يتبع هذه الحلقة.');

        $schedule->delete();

        return back()->with('success', 'تم حذف الموعد.');
    }
}
