<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\EducationClass;
use App\Models\Institute;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    protected function supervisorInstituteIds(): array
    {
        return auth()->user()
            ? auth()->user()->institutes()->pluck('institutes.id')->toArray()
            : [];
    }

    public function index(Request $request)
    {
        $user     = auth()->user();
        $roleName = $user->role->name ?? null;

        $query = Teacher::query()
            ->with([
                'user:id,email',
                'institutes:id,name',
            ])
            ->withCount('teachingClasses');

        if ($roleName === 'super admin') {
            // سوبر أدمن: يمكن فلترة بمعهد معيّن، وإلا الكل
            if ($instId = (int) $request->input('institute_id')) {
                $query->whereHas('institutes', function ($iq) use ($instId) {
                    $iq->where('institutes.id', $instId);
                });
            }
        } elseif ($roleName === 'admin') {
            // مشرف: قيود بمعاهده فقط
            $instIds = $this->supervisorInstituteIds();
            abort_if(empty($instIds), 403, 'لا تملك صلاحية على أي معهد.');
            $query->whereHas('institutes', function ($iq) use ($instIds) {
                $iq->whereIn('institutes.id', $instIds);
            });
        } else {
            abort(403, 'غير مصرّح');
        }

        // بحث
        if ($search = trim((string)$request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('teachers.first_name', 'like', "%{$search}%")
                    ->orWhere('teachers.last_name',  'like', "%{$search}%")
                    ->orWhere('teachers.phone',     'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%");
                    });
            });
        }

        // فلترة حلقة (مع ضمان انتمائها لمعاهد مسموحة)
        if ($classId = (int) $request->input('class_id')) {
            $query->whereHas('teachingClasses', function ($cq) use ($classId) {
                $cq->where('classes.id', $classId);
            });
        }

        // فرز
        $sort = $request->input('sort'); // name | classes_count | created_at
        $dir  = $request->input('direction', 'asc');

        if ($sort === 'name') {
            $query->orderBy('teachers.first_name', $dir)
                ->orderBy('teachers.last_name',  $dir);
        } elseif ($sort === 'classes_count') {
            $query->orderBy('teaching_classes_count', $dir);
        } elseif ($sort === 'created_at') {
            $query->orderBy('teachers.created_at', $dir);
        } else {
            $query->orderBy('teachers.first_name')->orderBy('teachers.last_name');
        }

        $teachers = $query->paginate(10)->withQueryString();

        // خيارات الفلاتر (مقيّدة حسب الدور)
        if ($roleName === 'super admin') {
            $institutes = Institute::orderBy('name')->get(['id','name']);
            $classes    = EducationClass::orderBy('name')->get(['id','name']);
        } else {
            $instIds    = $this->supervisorInstituteIds();
            $institutes = Institute::whereIn('id', $instIds)->orderBy('name')->get(['id','name']);
            $classes    = EducationClass::whereHas('subject', function ($q) use ($instIds) {
                $q->whereIn('institute_id', $instIds);
            })->orderBy('name')->get(['id','name']);
        }

        return view('supervisor.teachers.index', compact('teachers', 'institutes', 'classes'));
    }

    public function show(Teacher $teacher)
    {
        $user     = auth()->user();
        $roleName = $user->role->name ?? null;

        if ($roleName === 'super admin') {
            // مسموح
        } elseif ($roleName === 'admin') {
            // تأكد أن المدرس مرتبط بأحد معاهد المشرف
            $instIds = $this->supervisorInstituteIds();
            abort_if(empty($instIds), 403, 'لا تملك صلاحية على أي معهد.');
            $belongs = $teacher->institutes()->whereIn('institutes.id', $instIds)->exists();
            abort_if(!$belongs, 403, 'لا تملك صلاحية على هذا المدرّس.');
        } else {
            abort(403, 'غير مصرّح');
        }

        $teacher->load([
            'user:id,email',
            'institutes:id,name',
            'teachingClasses:id,name,user_id,subject_id,students_count,session_count',
            'teachingClasses.subject:id,name',
        ])->loadCount('teachingClasses');

        $stats = [
            'classes_count' => $teacher->teaching_classes_count,
            'students_sum'  => (int) $teacher->teachingClasses->sum('students_count'),
            'sessions_sum'  => (int) $teacher->teachingClasses->sum('session_count'),
        ];

        return view('supervisor.teachers.show', compact('teacher', 'stats'));
    }
}
