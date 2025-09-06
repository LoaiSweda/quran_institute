<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClasStudentsController extends Controller
{
    protected function currentInstitute()
    {

        return Institute::where('user_id', auth()->id())
            ->firstOrFail();
    }


    public function index(EducationClass $class)
    {

        $class->load('users.studentProfile');
        $students = $class->users()->where('users.role_id', '<>', 4)->get();

        return view('manager.classes.students.index', compact('class','students'));
    }


    public function create(EducationClass $class)
    {
        $inst = $this->currentInstitute();

        $added = $class->users()->pluck('users.id')->toArray();

        $subject = $class->subject; 

        $enrolledInSubject = [];
        if ($subject && $subject->is_active) {
            $otherClassIds = EducationClass::where('subject_id', $class->subject_id)
                ->pluck('id')
                ->toArray();

            if (!empty($otherClassIds)) {
                $enrolledInSubject = DB::table('users_classes')
                    ->whereIn('class_id', $otherClassIds)
                    ->pluck('user_id')
                    ->toArray();
            }
        }

        $exclude = array_unique(array_merge($added, $enrolledInSubject));

        $studentsQuery = $inst->users()
            ->wherePivot('role_institute', 'student')
            ->whereNotIn('users.id', $exclude)
            ->join('students', 'students.user_id', '=', 'users.id') 
            ->select([
                'users.id',
                'users.email',
                'students.first_name',
                'students.last_name',
            ])
            ->orderBy('students.first_name');

        $students = $studentsQuery->paginate(30);
        //$students = $studentsQuery->get();

        return view('manager.classes.students.create', compact('class','students'));
    }


    public function store(Request $request, EducationClass $class)
    {
        $inst = $this->currentInstitute();

        $data = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        DB::table('institute_user')->updateOrInsert(
            ['institute_id'=>$inst->id,'user_id'=>$data['user_id']],
            ['role_institute'=>'student']
        );

        $class->users()->attach($data['user_id']);

        return redirect()
            ->route('manager.classes.students.index', $class)
            ->with('success','تم إضافة الطالب إلى الحلقة');
    }

    public function destroy(EducationClass $class, User $user)
    {
        $class->users()->detach($user->id);

        return back()->with('success','تم إزالة الطالب من الحلقة');
    }
}
