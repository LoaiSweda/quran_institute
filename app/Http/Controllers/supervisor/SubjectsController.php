<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectsController extends Controller
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

        $query = Subject::query();

        if ($roleName === 'super admin') {
            if ($iid = (int) $request->input('institute_id')) {
                $query->where('institute_id', $iid);
            }
        } elseif ($roleName === 'admin') {
            $instIds = $this->supervisorInstituteIds();
            abort_if(empty($instIds), 403, 'لا تملك صلاحية على أي معهد.');
            $query->whereIn('institute_id', $instIds);
        } else {
            abort(403, 'غير مصرّح');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($sort = $request->input('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }

        $subjects = $query->paginate(10)->withQueryString();

        $institutes = ($roleName === 'super admin')
            ? Institute::orderBy('name')->get(['id','name'])
            : collect();

        return view('supervisor.subjects.index', compact('subjects', 'institutes'));
    }

    public function show(Subject $subject)
    {
        $user     = auth()->user();
        $roleName = $user->role->name ?? null;

        if ($roleName === 'super admin') {
        } elseif ($roleName === 'admin') {
            $instIds = $this->supervisorInstituteIds();
            abort_if(empty($instIds), 403, 'لا تملك صلاحية على أي معهد.');
            abort_if(!in_array($subject->institute_id, $instIds, true), 403, 'لا تملك صلاحية على هذه المادة.');
        } else {
            abort(403, 'غير مصرّح');
        }

        $subject->load([
            'institute.manager',
            'classes',
        ])->loadCount('classes');

        return view('supervisor.subjects.show', compact('subject'));
    }
}
