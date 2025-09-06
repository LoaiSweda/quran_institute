<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\SessionSchedule;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchedulesController extends Controller
{
    protected function myInstituteIds(): array
    {
        return auth()->user()
            ? auth()->user()->institutes()->pluck('institutes.id')->toArray()
            : [];
    }

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

    public function index(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $classes  = EducationClass::whereHas('subject', fn($q) => $q->where('institute_id', $inst->id))
            ->orderBy('name')->get(['id','name','subject_id','user_id']);
        $subjects = Subject::where('institute_id', $inst->id)
            ->orderBy('name')->get(['id','name']);

        $query = SessionSchedule::with(['educationClass.subject','educationClass.teacher'])
            ->whereHas('educationClass.subject', function($q) use ($inst) {
                $q->where('institute_id', $inst->id);
            });

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->integer('class_id'));
        }
        if ($request->filled('subject_id')) {
            $query->whereHas('educationClass', function($q) use ($request) {
                $q->where('subject_id', $request->integer('subject_id'));
            });
        }
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->get('day_of_week'));
        }

        $dir = $request->get('direction') === 'desc' ? 'desc' : 'asc';
        $sort = $request->get('sort');
        if ($sort === 'day') {
            $query->orderBy('day_of_week', $dir)->orderBy('start_time', $dir);
        } elseif ($sort === 'start') {
            $query->orderBy('start_time', $dir);
        } elseif ($sort === 'teacher') {
            $query->select('session_schedules.*')
                ->join('classes','classes.id','=','session_schedules.class_id')
                ->join('teachers','teachers.user_id','=','classes.user_id')
                ->orderBy('teachers.first_name', $dir)
                ->orderBy('teachers.last_name', $dir);
        } else {
            $query->orderBy('day_of_week')->orderBy('start_time');
        }

        $schedules = $query->paginate(20)->withQueryString();

        return view('supervisor/schedules/index', [
            'inst'      => $inst,
            'classes'   => $classes,
            'subjects'  => $subjects,
            'schedules' => $schedules,
        ]);
    }
}
