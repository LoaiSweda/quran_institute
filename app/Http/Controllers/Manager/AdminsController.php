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

    /**
     * فهرس المشرفين (بحث + فرز + صفحات)
     */
    public function index(Request $request)
    {
        $query = $this->adminsQueryForCurrentInstitute();

        // بحث بالاسم أو البريد
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%");
                    });
            });
        }

        // فرز
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

    /**
     * عرض نموذج الإنشاء + قائمة مشرفي المعهد أسفل الصفحة
     */
    public function create()
    {
        $admins = $this->adminsQueryForCurrentInstitute()
            ->orderByDesc('id')->get();

        return view('manager.admins.create', compact('admins'));
    }

    /**
     * حفظ مشرف جديد:
     * users + admins + institute_user(role_institute, timestamps)
     */
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
            // إنشاء المستخدم
            $user = User::create([
                'name'     => $data['first_name'].' '.$data['last_name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id'  => 2, // admin
            ]);

            // رفع الصورة إن وجدت
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('admins', 'public');
            }

            // إنشاء سجل admins
            $admin = Admin::create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'] ?? null,
                'birthdate'  => $data['birthdate'] ?? null,
                'user_id'    => $user->id,
                'image'      => $imagePath,
            ]);

            // ربطه بالمعهد في institute_user مع role_institute + timestamps
            if (!$user->institutes()->where('institute_id', $inst->id)->exists()) {
                $user->institutes()->attach($inst->id, [
                    'role_institute' => 'admin',
                ]);
                // لأن علاقة institutes() تحتوي withTimestamps()
                // فسيتم تعبئة created_at/updated_at تلقائيًا.
            }
        });

        return redirect()->route('manager.admins.index')
            ->with('success', 'تم إضافة المشرف بنجاح.');
    }

    /**
     * عرض تفاصيل مشرف
     */
    public function show(Admin $admin)
    {
        // تأكيد الانتماء للمعهد الحالي
        $this->adminsQueryForCurrentInstitute()->findOrFail($admin->id);

        return view('manager.admins.show', compact('admin'));
    }

    /**
     * نموذج تعديل + قائمة مشرفي المعهد أسفل الصفحة
     */
    public function edit(Admin $admin)
    {
        // تأكيد الانتماء للمعهد الحالي
        $this->adminsQueryForCurrentInstitute()->findOrFail($admin->id);

        $admins = $this->adminsQueryForCurrentInstitute()
            ->orderByDesc('id')->get();

        return view('manager.admins.edit', compact('admin', 'admins'));
    }

    /**
     * تحديث بيانات المشرف
     */
    public function update(Request $request, Admin $admin)
    {
        // تأكيد الانتماء للمعهد الحالي
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
            // تحديث المستخدم
            $admin->user->update([
                'name'     => $data['first_name'].' '.$data['last_name'],
                'email'    => $data['email'],
                'password' => $data['password']
                    ? Hash::make($data['password'])
                    : $admin->user->password,
            ]);

            // تبديل الصورة إن وُجدت ملف جديد
            $imagePath = $admin->image;
            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('admins', 'public');
            }

            // تحديث admin
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

    /**
     * حذف مشرف (يشمل حذف الصورة والربط بالمعهد الحالي فقط)
     */
    public function destroy(Admin $admin)
    {
        // تأكيد الانتماء للمعهد الحالي
        $inst = $this->currentInstitute();
        $this->adminsQueryForCurrentInstitute()->findOrFail($admin->id);

        DB::transaction(function () use ($admin, $inst) {
            // حذف الصورة إن وُجدت
            if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                Storage::disk('public')->delete($admin->image);
            }

            // فك الربط من هذا المعهد فقط (احتياطًا)
            $admin->user->institutes()->detach($inst->id);

            // حذف المستخدم ثم سجل admin
            $admin->user->delete();
            $admin->delete();
        });

        return redirect()->route('manager.admins.index')
            ->with('success', 'تم حذف المشرف بنجاح.');
    }
}
