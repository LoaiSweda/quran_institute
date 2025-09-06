<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Storage;
//use BaconQrCode\Renderer\ImageRenderer;
//use BaconQrCode\Renderer\RendererStyle\RendererStyle;
//use BaconQrCode\Renderer\Image\SvgImageBackEnd;
//use BaconQrCode\Writer;

class ClassesController extends Controller
{
    /**
     *  403
     *
     * @return Institute
     */
    protected function currentInstitute(): Institute
    {
        $inst = Institute::where('user_id', auth()->id())->first();
        abort_if(!$inst, 403, 'لا تملك صلاحية على أي معهد.');
        return $inst;
    }

    public function index()
    {
        $inst = $this->currentInstitute();

        $classes = EducationClass::whereHas('subject', fn($q)=>
        $q->where('institute_id',$inst->id))
            ->with(['subject','teacher'])
            ->paginate(20);

        return view('manager.classes.index', compact('classes'));
    }


    public function create()
    {
        $inst     = $this->currentInstitute();
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



        $classes = EducationClass::whereHas('subject', function($q) use($inst) {
            $q->where('institute_id', $inst->id);
        })
            ->with(['subject','teacher'])
            ->paginate(10);

        return view('manager.classes.create', compact('subjects','teachers','classes'));
    }


    public function store(Request $request)
    {
        $inst = $this->currentInstitute();

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'subject_id'     => 'required|exists:subjects,id',
            'user_id'        => 'required|exists:users,id',
            'students_count' => 'required|integer|min:1',
            'schedules'      => 'nullable|array',
            'schedules.*.day_of_week' => 'required_with:schedules|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'schedules.*.start_time'  => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time'    => 'required_with:schedules|date_format:H:i|after:schedules.*.start_time',

        ]);
        DB::transaction(function() use ($data, $inst, $request, &$cls) {

            $cls = EducationClass::create($data);

            DB::table('institute_user')->updateOrInsert(
                [
                    'institute_id' => $inst->id,
                    'user_id' => $data['user_id'],
                ],
                [
                    'role_institute' => 'teacher',
                ]
            );


            $cls->users()->syncWithoutDetaching($data['user_id']);


            if (!empty($data['schedules'])) {
                foreach ($data['schedules'] as $sch) {
                    $cls->sessionSchedules()->create($sch);
                }
            }
        });

        return redirect()
            ->route('manager.classes.index')
            ->with('success', 'تم إنشاء الحلقة وتحديد جداول المواعيد بنجاح');
    }

    public function show(\App\Models\EducationClass $class)
    {
        $class->load([
            'subject',
            'teacher',
            'sessions',
            'exams.student',
            'progress',
            'enrolledStudents.student', 
        ]);

        $students = $class->enrolledStudents->pluck('student');

        $studentsCount      = $students->count();
        $presentPercentage  = $class->present_percentage ?? 0;
        $sessionsHeld       = $class->sessions->count();
        $totalPoints        = $class->exams->sum('points');

        return view('manager.classes.show', compact(
            'class','students','studentsCount','presentPercentage','sessionsHeld','totalPoints'
        ));
    }

    public function edit(EducationClass $class)
    {
        $inst = $this->currentInstitute();
        abort_if($class->subject->institute_id !== $inst->id, 403);



        $class->load(['subject', 'teacher', 'sessionSchedules']);

        $subjects = Subject::where('institute_id', $inst->id)->get();


        $teachers = DB::table('institute_user')
            ->join('teachers','institute_user.user_id','=','teachers.user_id')
            ->where('institute_user.institute_id',$inst->id)
            ->where('institute_user.role_institute','teacher')
            ->select([
                'teachers.user_id as id',
                'teachers.first_name',
                'teachers.last_name',
            ])
            ->get();

        return view('manager.classes.edit', compact('class','subjects','teachers'));
    }

    public function update(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute();
        abort_if($class->subject->institute_id !== $inst->id, 403);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'subject_id'     => 'required|exists:subjects,id',
            'user_id'        => 'required|exists:users,id',
            'students_count' => 'required|integer|min:1',
        ]);

        DB::transaction(function() use ($class, $data){
            $class->update($data);


            $keep = [];

            if (!empty($data['schedules'])) {
                foreach ($data['schedules'] as $sch) {
                    if (!empty($sch['id'])) {


                        $schedule = $class->sessionSchedules()->findOrFail($sch['id']);
                        $schedule->update($sch);
                        $keep[] = $schedule->id;
                    } else {

                        $new = $class->sessionSchedules()->create($sch);
                        $keep[] = $new->id;
                    }
                }
            }

            $class->sessionSchedules()
                ->whereNotIn('id', $keep)
                ->delete();
        });

        return back()->with('success', 'تم حفظ التعديلات على الحلقة وجداول المواعيد');
    }
  
    public function destroy(EducationClass $class)
    {
        $inst = $this->currentInstitute();
        abort_if($class->subject->institute_id !== $inst->id, 403);

        $class->delete();

        return redirect()
            ->route('manager.classes.index')
            ->with('success', 'تم حذف الحلقة');
    }
}
