<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuardiansController extends Controller
{
    /**
     * 4.4.6.3 عرض جميع أولياء الأمور
     */
    public function index(Request $request)
    {
        $query = Guardian::with('students', 'user');


        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($sort = $request->input('sort')) {
            [$field, $dir] = explode('_', $sort);
            $query->orderBy($field, $dir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $guardians = $query->paginate(15);

        return view('manager.guardians.index', compact('guardians'));
    }

    /**
     * 4.4.6.1 عرض نموذج إنشاء حساب ولي أمر
     */
    public function create()
    {

        $guardians = Guardian::with('user', 'students')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('manager.guardians.create', compact('guardians'));
    }

    /**
     * 4.4.6.1 حفظ حساب ولي أمر جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'address'   => 'nullable|string|max:500',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|confirmed|min:6',
        ]);

        // إنشاء حساب المستخدم
        $user = User::create([
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => 6, // guardian
        ]);

        // إنشاء الوصي
        Guardian::create([
            'user_id'   => $user->id,
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'phone'     => $data['phone'],
            'address'   => $data['address'] ?? null,
        ]);

        // ربط الوصي بالمعهد الحالي
        $institute = auth()->user()->institute;
        if ($institute) {
            $institute->users()->attach($user->id, [
                'role_institute' => 'guardian',
            ]);
        }

        return redirect()
            ->route('manager.guardians.index')
            ->with('success', 'تم إنشاء حساب وليّ الأمر وربطه بالمعهد بنجاح.');
    }

    /**
     * 4.4.6.4 عرض تفاصيل ولي أمر
     */
    public function show(Guardian $guardian)
    {
        $guardian->load('students', 'user');
        return view('manager.guardians.show', compact('guardian'));
    }

    /**
     * 4.4.6.1 عرض نموذج تعديل ولي أمر
     */
    public function edit(Guardian $guardian)
    {
        $guardian->load('user');
        return view('manager.guardians.edit', compact('guardian'));
    }

    /**
     * 4.4.6.1 تحديث بيانات ولي الأمر وحسابه
     */
    public function update(Request $request, Guardian $guardian)
    {
        $data = $request->validate([
            'firstname'            => 'required|string|max:255',
            'lastname'             => 'required|string|max:255',
            'phone'                => 'required|string|max:20',
            'address'              => 'nullable|string|max:500',
            'email'                => 'required|email|unique:users,email,' . $guardian->user_id,
            'password'             => 'nullable|confirmed|min:6',
        ]);

        $guardian->update([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'phone'     => $data['phone'],
            'address'   => $data['address'] ?? null,
        ]);

        $user = $guardian->user;
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()
            ->route('manager.guardians.index')
            ->with('success', 'تم تحديث بيانات وليّ الأمر بنجاح.');
    }

    /**
     * 4.4.6.2 حذف/تعطيل ولي أمر
     */
    public function destroy(Guardian $guardian)
    {

        $user = $guardian->user;
        $guardian->delete();
        $user->delete();

        return redirect()
            ->route('manager.guardians.index')
            ->with('success', 'تم حذف وليّ الأمر بنجاح.');
    }
}
