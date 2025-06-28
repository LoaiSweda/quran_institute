<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreInstituteRequest;
use App\Http\Requests\SuperAdmin\UpdateInstituteRequest;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstituteController extends Controller
{
    public function index(Request $request)
    {
        $query = Institute::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($sort = $request->get('sort')) {
            $direction = $request->get('direction', 'asc');
            $query->orderBy($sort, $direction);
        }

        $institutes = $query->paginate(10)->withQueryString();
        return view('super-admin.institutes.index', compact('institutes'));
    }

    public function create()
    {
        // جلب كل المستخدمين المحتملين ليصبحوا مدراء معاهد
        $managers = \App\Models\User::whereHas('role', fn($q)=> $q->where('name','institute manager'))->get();
        return view('super-admin.institutes.form', [
            'institute' => new Institute,
            'managers'  => $managers,
            'action'    => route('super-admin.institutes.store'),
            'method'    => 'POST',
        ]);
    }

    public function store(StoreInstituteRequest $request)
    {
        $data = $request->validated();
        if ($file = $request->file('image')) {
            $data['image'] = $file->store('institutes', 'public');
        }
        Institute::create($data);
        return redirect()->route('super-admin.institutes.index')
            ->with('success','تم إنشاء المعهد بنجاح');
    }

    public function show(Institute $institute)
    {
        $stats = [
            'subjects_count' => $institute->subjects()->count(),
            'classes_count'  => $institute->classes()->count(),
            'admins_count'   => $institute->admins()->count(),
        ];
        return view('super-admin.institutes.show', compact('institute','stats'));
    }

    public function edit(Institute $institute)
    {
        $managers = \App\Models\User::whereHas('role', fn($q)=> $q->where('name','institute manager'))->get();
        return view('super-admin.institutes.form', [
            'institute' => $institute,
            'managers'  => $managers,
            'action'    => route('super-admin.institutes.update', $institute),
            'method'    => 'PUT',
        ]);
    }

    public function update(UpdateInstituteRequest $request, Institute $institute)
    {
        $data = $request->validated();
        if ($file = $request->file('image')) {
            // حذف القديم إذا كان موجود
            if ($institute->image) {
                Storage::disk('public')->delete($institute->image);
            }
            $data['image'] = $file->store('institutes', 'public');
        }
        $institute->update($data);
        return redirect()->route('super-admin.institutes.index')
            ->with('success','تم تحديث بيانات المعهد');
    }

    public function destroy(Institute $institute)
    {
        $institute->delete(); // soft delete
        return back()->with('success','تم تعطيل المعهد');
    }
}
