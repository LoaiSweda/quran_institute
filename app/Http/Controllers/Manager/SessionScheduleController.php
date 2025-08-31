<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\SessionSchedule;
use App\Models\Subject;
use Illuminate\Http\Request;

class SessionScheduleController extends Controller
{
    protected function currentInstitute(): Institute
    {
        $inst = Institute::where('user_id', auth()->id())->first();
        abort_if(!$inst, 403, 'لا تملك صلاحية على أي معهد.');
        return $inst;
    }


    protected function currentInstituteClass(int $classId): EducationClass
    {
        $class = EducationClass::with('subject')
            ->findOrFail($classId);

        abort_if(
            $class->subject->institute_id !== auth()->user()->institute->id,
            403,
            'لا تملك صلاحية على هذه الحلقة.'
        );
        return $class;
    }
    public function index(Request $request)
    {
        $inst = $this->currentInstitute();

        $classes = EducationClass::whereHas('subject', function($q) use ($inst) {
            $q->where('institute_id', $inst->id);
        })->get();



        $subjects = Subject::where('institute_id', $inst->id)->get();


        $query = SessionSchedule::with(['educationClass.subject','educationClass.teacher'])
            ->whereHas('educationClass.subject', function($q) use ($inst) {
                $q->where('institute_id', $inst->id);
            });



        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('subject_id')) {
            $query->whereHas('educationClass', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }



        $dir = $request->direction === 'desc' ? 'desc' : 'asc';
        if ($request->sort === 'day') {
            $query->orderBy('day_of_week', $dir);
        } elseif ($request->sort === 'start') {
            $query->orderBy('start_time', $dir);
        } elseif ($request->sort === 'teacher') {
            // نفترض أن teacher relation موجودة ويملك first_name
            $query->join('classes', 'classes.id', '=', 'session_schedules.class_id')
                ->join('teachers','teachers.user_id','=','classes.user_id')
                ->orderBy('teachers.first_name', $dir)
                ->select('session_schedules.*');
        } else {
            $query->orderBy('day_of_week')->orderBy('start_time');
        }

        $schedules = $query->paginate(20)->withQueryString();

        return view('manager.classes.schedules.index', [
            'inst'      => $inst,
            'classes'   => $classes,
            'subjects'  => $subjects,
            'schedules' => $schedules,
        ]);
    }


    public function create($classId)
    {
        $class = $this->currentInstituteClass($classId);

        $class->load(['sessionSchedules' => function($q){
            $q->orderBy('day_of_week')->orderBy('start_time');
        }]);

        return view('manager.classes.schedules.create', compact('class'));
    }


    public function store(Request $request, $classId)
    {
        $class = $this->currentInstituteClass($classId);

        $data = $request->validate([
            'day_of_week' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
        ]);

        // ابحث عن user_id للمعلّم من عمود class.user_id
        $teacherUserId = $class->user_id; // أو optional($class->teacher)->user_id

        // سلامة: إذا لم يكن هناك معلّم، ارجع خطأ أو استخدم الـ manager كمحافظ
        if (!$teacherUserId) {
            return back()->withErrors(['teacher' => 'لم يتم تعيين معلّم لهذه الحلقة.']);
        }

        // نستخدم علاقة sessionSchedules لإنشاء السجل (تملأ class_id تلقائياً)
        $class->sessionSchedules()->create(array_merge($data, [
            'user_id' => $teacherUserId,
        ]));

        return redirect()
            ->route('manager.classes.edit', $class->id)
            ->with('success', 'تم إضافة الموعد بنجاح');
    }



    public function edit(int $classId, SessionSchedule $schedule)
    {


        $class = $this->currentInstituteClass($classId);
        abort_if($schedule->class_id !== $class->id, 403);


        $class->load(['sessionSchedules' => function($q) {
            $q->orderBy('day_of_week')->orderBy('start_time');
        }]);


        return view('manager.classes.schedules.edit', [
            'class'    => $class,
            'schedule' => $schedule,
        ]);
    }

    public function update(Request $request, $classId, SessionSchedule $schedule)
    {
        $class = $this->currentInstituteClass($classId);
        abort_if($schedule->class_id !== $class->id, 403);

        $data = $request->validate([
            'day_of_week' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
        ]);

        // خيار: اجعل user_id يعكس معلّم الحلقة الحالي
        $teacherUserId = $class->user_id;
        if ($teacherUserId) {
            $data['user_id'] = $teacherUserId;
        }

        $schedule->update($data);

        return redirect()
            ->route('manager.classes.edit', $class->id)
            ->with('success', 'تم تحديث الموعد بنجاح');
    }


    public function destroy($classId, SessionSchedule $schedule)
    {
        $class = $this->currentInstituteClass($classId);
        abort_if($schedule->class_id !== $class->id, 403);

        $schedule->delete();

        return back()->with('success', 'تم حذف الموعد');
    }
}
