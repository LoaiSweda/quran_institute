<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminsController extends Controller
{
    /**
     * معهد مدير الجلسة الحالي
     */
    protected function currentInstitute(): Institute
    {
        return Institute::where('user_id', auth()->id())->firstOrFail();
    }

    /**
     * استعلام جاهز لمشرفي المعهد الحالي
     */
    protected function adminsQueryForCurrentInstitute()
    {
        $inst = $this->currentInstitute();

        return Admin::with('user')
            ->whereHas('user.institutes', function ($q) use ($inst) {
                $q->where('institute_id', $inst->id);
            });
    }

    public function index(Request $request)
    {
        $query = $this->adminsQueryForCurrentInstitute();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $allowedSorts = ['first_name', 'last_name', 'birthdate'];
        $sort = $request->get('sort');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        $admins = $query->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('manager.admins.index', compact('admins'));
    }

    public function create()
    {
        $admins = $this->adminsQueryForCurrentInstitute()
            ->orderByDesc('id')->get();

        return view('manager.admins.create', compact('admins'));
    }

    
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
            'birthdate'  => 'nullable|date',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'image'      => 'nullable|image|max:2048',
        ]);

        $inst = $this->currentInstitute();

        DB::transaction(function () use ($data, $request, $inst) {
            $user = User::create([
                'name'     => $data['first_name'].' '.$data['last_name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id'  => 2, 
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('admins', 'public');
            }

            $admin = Admin::create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'birthdate'  => $data['birthdate'] ?? null,
                'user_id'    => $user->id,
                'image'      => $imagePath,
            ]);

            if (!$user->institutes()->where('institute_id', $inst->id)->exists()) {
                $user->institutes()->attach($inst->id, [
                    'role_institute' => 'admin',
                ]);
            }
        });

        return redirect()->route('manager.admins.index')
            ->with('success', 'تم إضافة المشرف بنجاح.');
    }

    public function show(Admin $admin)
    {
        $this->adminsQueryForCurrentInstitute()->findOrFail($admin->id);

        return view('manager.admins.show', compact('admin'));
    }

    
    public function edit(Admin $admin)
    {
        $this->adminsQueryForCurrentInstitute()->findOrFail($admin->id);

        $admins = $this->adminsQueryForCurrentInstitute()
            ->orderByDesc('id')->get();

        return view('manager.admins.edit', compact('admin', 'admins'));
    }

    public function update(Request $request, Admin $admin)
    {
        $this->adminsQueryForCurrentInstitute()->findOrFail($admin->id);

        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
            'birthdate'  => 'nullable|date',
            'email'      => 'required|email|unique:users,email,' . $admin->user_id,
            'password'   => 'nullable|string|min:6|confirmed',
            'image'      => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($data, $admin, $request) {
            $admin->user->update([
                'name'     => $data['first_name'].' '.$data['last_name'],
                'email'    => $data['email'],
                'password' => $data['password']
                    ? Hash::make($data['password'])
                    : $admin->user->password,
            ]);

            $imagePath = $admin->image;
            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('admins', 'public');
            }

            $admin->update([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'birthdate'  => $data['birthdate'] ?? null,
                'image'      => $imagePath,
            ]);
        });

        return redirect()->route('manager.admins.index')
            ->with('success', 'تم تعديل المشرف بنجاح.');
    }

    public function destroy(Admin $admin)
    {
        $inst = $this->currentInstitute();
        $this->adminsQueryForCurrentInstitute()->findOrFail($admin->id);

        DB::transaction(function () use ($admin, $inst) {
            if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                Storage::disk('public')->delete($admin->image);
            }

            $admin->user->institutes()->detach($inst->id);

            $admin->user->delete();
            $admin->delete();
        });

        return redirect()->route('manager.admins.index')
            ->with('success', 'تم حذف المشرف بنجاح.');
    }
}
