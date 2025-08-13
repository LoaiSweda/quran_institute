<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreInstituteRequest;
use App\Http\Requests\SuperAdmin\UpdateInstituteRequest;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\SessionSchedule;


class InstituteController extends Controller
{
    public function index(Request $request)
    {
        $query = Institute::query();
        if ($s = $request->get('search')) {
            $query->where('name', 'like', "%{$s}%");
        }
        if ($sort = $request->get('sort')) {
            $dir = $request->get('direction', 'asc');
            $query->orderBy($sort, $dir);
        }
        $institutes = $query->paginate(10)->withQueryString();
        return view('super-admin.institutes.index', compact('institutes'));
    }

    public function create()
    {
        $newManagerId = session('new_manager_id');

        $managers = User::whereHas('role', fn($q) =>
        $q->whereIn('name', ['admin', 'institute manager'])
        )->get();

        $institutes = Institute::latest()
            ->paginate(10);

        return view('super-admin.institutes.form', [
            'institute'     => new Institute,
            'managers'      => $managers,
            'institutes'    => $institutes,
            'action'        => route('super-admin.institutes.store'),
            'method'        => 'POST',
            'newManagerId'  => $newManagerId,
        ]);
    }

    public function store(StoreInstituteRequest $request)
    {
        $data = $request->validated();

        if ($f = $request->file('image')) {
            $data['image'] = $f->store('institutes', 'public');
        }

        $institute = Institute::create($data);

        return redirect()
            ->route('super-admin.institutes.create')
            ->with('success', 'تم إنشاء المعهد وربط المدير بنجاح');
    }



    public function show(Institute $institute)
    {
        $subjectsCount = $institute->subjects()->count();

        $classesCount  = $institute->classes()->count();

        $classIds = $institute
            ->classes()
            ->pluck('classes.id');

        $adminsCount   = $institute->admins()->count();

        $teachersCount = \App\Models\SessionSchedule::whereIn('class_id', $classIds)
            ->distinct('user_id')
            ->count('user_id');

        return view('super-admin.institutes.show', compact(
            'institute',
            'subjectsCount',
            'classesCount',
            'adminsCount',
            'teachersCount'
        ));
    }


    public function edit(Institute $institute)
    {
        $managers = User::whereHas('role', fn($q) =>
        $q->whereIn('name', ['admin', 'institute manager'])
        )->get();

        return view('super-admin.institutes.form', [
            'institute'     => $institute,
            'managers'      => $managers,
            'action'        => route('super-admin.institutes.update', $institute),
            'method'        => 'PUT',
            'newManagerId'  => null,
            'institutes'    => Institute::latest()->paginate(10),
        ]);
    }

    public function update(UpdateInstituteRequest $request, Institute $institute)
    {
        $data = $request->validated();
        if ($f = $request->file('image')) {
            if ($institute->image) {
                Storage::disk('public')->delete($institute->image);
            }
            $data['image'] = $f->store('institutes', 'public');
        }
        $institute->update($data);
        return redirect()
            ->route('super-admin.institutes.create')
            ->with('success','تم تحديث بيانات المعهد');
    }

    public function destroy(Institute $institute)
    {
        $institute->delete();
        return back()->with('success','تم تعطيل المعهد');
    }

    // --------------------------------
    public function createManager()
    {
        return view('super-admin.institutes.manager-form');
    }

    public function storeManager(Request $request)
    {
        $data = $request->validate([
            'email'     => ['required','email','unique:users,email'],
            'password'  => ['required','min:6','confirmed'],
            'name'      => ['nullable','string'],
        ]);

        $roleId = \App\Models\Role::where('name','institute manager')
            ->firstOrFail()
            ->id;

        $user = User::create([
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $roleId,
            'name'     => $data['name'] ?? null,
        ]);

        session()->flash('new_manager_id', $user->id);

        return redirect()
            ->route('super-admin.institutes.create')
            ->with('success','تم إنشاء مدير المعهد بنجاح');
    }
}
