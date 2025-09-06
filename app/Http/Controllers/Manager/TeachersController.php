<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Institute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeachersController extends Controller
{
    
    protected function institute()
    {
        return Institute::where('user_id', auth()->id())->firstOrFail();
    }
    public function createUser()
    {
        return view('manager.teachers.create_user');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id'  => 4,
        ]);


        session()->flash('new_teacher_user_id', $user->id);

        return redirect()
            ->route('manager.teachers.create')
            ->with('success','تم إنشاء المستخدم بنجاح، يمكنك الآن استكمال بيانات المدرّس.');
    }


    public function index(Request $request)
    {
        $institute = $this->institute();

        $query = $institute->teachers();

        if ($q = $request->input('search')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%");
            });
        }

        if ($sort = $request->input('sort')) {
            $dir = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
            if (in_array($sort, ['first_name','last_name','phone','birthdate'])) {
                $query->orderBy($sort, $dir);
            }
        }

        $teachers = $query->paginate(10);

        return view('manager.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $inst = $this->institute();

        $classes = $inst->classes()->get();

        $teachers = $inst->teachers()->with('user')->orderBy('first_name')->get();

        return view('manager.teachers.create', compact('classes', 'teachers'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
        ]);

        DB::transaction(function () use ($data, &$teacher) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => 4,
            ]);

            if (isset($data['image'])) {
                $data['image'] = request()->file('image')->store('teachers', 'public');
            }

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'image' => $data['image'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'birthdate' => $data['birthdate'] ?? null,
            ]);

            $this->institute()
                ->teachers()
                ->attach($user->id, ['role_institute' => 'teacher']);
        });
        return redirect()
            ->route('manager.teachers.index')
            ->with('success', 'تم إنشاء المدرّس وحسابه وربطه بالمعهد بنجاح.');
    }
   
    public function show($id)
    {
        $teacher = $this->institute()
            ->teachers()
            ->with('teachingClasses.sessionSchedules')
            ->with('teachingClasses.subject') 

            ->findOrFail($id);


        return view('manager.teachers.show', compact('teacher'));
    }
  
  
    public function edit($id)
    {
        $teacher = $this->institute()
            ->teachers()
            ->findOrFail($id);

        $teachers = $this->institute()
            ->teachers()
            ->with('user')
            ->get();

        return view('manager.teachers.edit', compact('teacher', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $teacher = $this->institute()
            ->teachers()
            ->findOrFail($id);

        $data = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'image'        => 'nullable|image|max:2048',
            'phone'        => 'nullable|string|max:20',
            'address'      => 'nullable|string|max:255',
            'birthdate'    => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($teacher->image) {
                Storage::disk('public')->delete($teacher->image);
            }
            $data['image'] = $request->file('image')->store('teachers', 'public');
        }

        $teacher->update($data);

        return redirect()
            ->route('manager.teachers.edit', $teacher)
            ->with('success', 'تم تحديث بيانات المدرس بنجاح.');
    }


    public function destroy($id)
    {
        $teacher = $this->institute()
            ->teachers()
            ->findOrFail($id);

        $teacher->institutes()->detach($this->institute()->id);

        return redirect()
            ->route('manager.teachers.index')
            ->with('success', 'تم تعطيل المدرس بنجاح.');
    }
}
