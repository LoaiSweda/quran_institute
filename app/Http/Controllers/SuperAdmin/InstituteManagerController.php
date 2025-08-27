<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institute;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Admin;

class InstituteManagerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'institute', 'admin'])
            ->whereHas('role', fn($q) => $q->where('name', 'institute manager'));

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                ->orWhere('id', $search);
            });
        }

        if ($sort = $request->input('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        $managers = $query->paginate(15)->withQueryString();

        return view('super-admin.managers.index', compact('managers'));
    }

    public function create()
    {
        $user = new User();
        $institutes = Institute::orderBy('name')->get();
        return view('super-admin.managers.create', compact('user', 'institutes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','min:8','confirmed'],
            'institute_id' => ['nullable','exists:institutes,id'],
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'birthdate' => ['nullable','date'],
            'image' => ['nullable','image','max:2048'],
        ]);

        $role = Role::where('name', 'institute manager')->firstOrFail();

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
        ]);

        // أنشئ ملف الـ Admin المرتبط
        $adminData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'birthdate' => $request->birthdate,
        ];

        if ($request->hasFile('image')) {
            $adminData['image'] = $request->file('image')->store('admin_profiles', 'public');
        }

        $user->admin()->create($adminData);

        // تعيين المعهد إذا وُجد
        if ($request->filled('institute_id')) {
            $institute = Institute::find($request->institute_id);
            $institute->user_id = $user->id;
            $institute->save();
        }

        return redirect()->route('super-admin.managers.index')
            ->with('success', 'تم إنشاء مدير المعهد بنجاح.');
    }

    public function edit(User $user)
    {
        $institutes = Institute::orderBy('name')->get();
        return view('super-admin.managers.edit', [
            'user' => $user,
            'institutes' => $institutes,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','min:8','confirmed'],
            'institute_id' => ['nullable','exists:institutes,id'],
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'birthdate' => ['nullable','date'],
            'image' => ['nullable','image','max:2048'],
        ]);

        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // تحديث أو إنشاء Admin profile
        $adminData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'birthdate' => $request->birthdate,
        ];

        $admin = $user->admin;

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا أحببت (اختياري)
            if ($admin && $admin->image) {
                \Storage::disk('public')->delete($admin->image);
            }
            $adminData['image'] = $request->file('image')->store('admin_profiles', 'public');
        }

        if ($admin) {
            $admin->update($adminData);
        } else {
            $user->admin()->create($adminData);
        }

        // تحديث تعيين المعهد
        if ($request->filled('institute_id')) {
            $institute = Institute::find($request->institute_id);
            $institute->user_id = $user->id;
            $institute->save();
        }

        return redirect()->route('super-admin.managers.index')
            ->with('success', 'تم تحديث بيانات مدير المعهد.');
    }


    public function destroy(User $user)
    {
        // فك الربط عن المعهد إذا كان مرتبطاً بهذا المدير
        if ($institute = $user->institute) {
            $institute->update(['user_id' => null]);
        }

        // حذف المدير (أو soft delete إذا مفعل)
        $user->delete();

        return back()->with('success', 'تم حذف مدير المعهد.');
    }


    public function show(User $user)
    {
        return view('super-admin.managers.show', compact('user'));
    }

    public function unassignInstitute(User $user)
    {
        if ($institute = $user->institute) {
            $institute->update(['user_id' => null]);
        }

        return back()->with('success', 'تم إلغاء تعيين المعهد لهذا المدير.');
    }

}
