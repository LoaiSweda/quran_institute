<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Institute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TeachersController extends Controller
{
    /**
     * استخرج معهد مدير الجلسة.
     */
    protected function institute()
    {
        return Institute::where('user_id', auth()->id())->firstOrFail();
    }
    public function createUser()
    {
        return view('manager.teachers.create_user');
    }

    /**
     *  حفظ بيانات المستخدم الجديد ثم إعادة توجيه إلى صفحة إنشاء المدرّس الأصلي
     */
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
            'role_id'  => 4,           // <––– هنا نمرّر role_id للمدرّس
        ]);

        // لو تستخدم حزمة صلاحيات لا حاجة لـ assignRole مجدداً

        session()->flash('new_teacher_user_id', $user->id);

        return redirect()
            ->route('manager.teachers.create')
            ->with('success','تم إنشاء المستخدم بنجاح، يمكنك الآن استكمال بيانات المدرّس.');
    }


    /**
     * 4.4.2.4 عرض جميع المدرّسين المرتبطين بالمعهد
     */
    public function index(Request $request)
    {
        $institute = $this->institute();

        // يبدأ الاستعلام من علاقة المعهد -> المدرّسون عبر pivot
        $query = $institute->teachers();

        // بحث نصي باسم الأول أو الأخير
        if ($q = $request->input('search')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%");
            });
        }

        // فرز بحسب أي من الحقول المسموح بها
        if ($sort = $request->input('sort')) {
            $dir = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
            if (in_array($sort, ['first_name','last_name','phone','birthdate'])) {
                $query->orderBy($sort, $dir);
            }
        }

        $teachers = $query->paginate(10);

        return view('manager.teachers.index', compact('teachers'));
    }

    /**
     * 4.4.2.1 عرض نموذج إضافة مدرس
     */
    public function create()
    {
        $users = User::where('role_id', 4)->get();

        return view('manager.teachers.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'first_name'=> 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'image'     => 'nullable|image|max:2048',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('teachers', 'public');
        }

        // إنشاء المدرّس وتخزين الـ user_id
        $teacher = Teacher::create($data);

        // ربطه بالمعهد عبر pivot (role_institute = 'teacher')
        $teacher->institutes()->attach(
            $this->institute()->id,
            ['role_institute' => 'teacher']
        );

        return redirect()
            ->route('manager.teachers.index')
            ->with('success', 'تم إضافة المدرّس بنجاح.');
    }

    /**
     * 4.4.2.5 عرض تفاصيل مدرس محدد
     */
    public function show($id)
    {
        $teacher = $this->institute()
            ->teachers()
            ->findOrFail($id);

        return view('manager.teachers.show', compact('teacher'));
    }

    /**
     * 4.4.2.2 عرض نموذج تعديل بيانات مدرس
     */
    public function edit($id)
    {
        $teacher = $this->institute()
            ->teachers()
            ->findOrFail($id);

        return view('manager.teachers.edit', compact('teacher'));
    }

    /**
     * 4.4.2.2 حفظ تعديلات مدرس
     */
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
            // حذف الصورة القديمة إن وجدت
            if ($teacher->image) {
                Storage::disk('public')->delete($teacher->image);
            }
            $data['image'] = $request->file('image')->store('teachers', 'public');
        }

        $teacher->update($data);

        return redirect()
            ->route('manager.teachers.show', $teacher)
            ->with('success', 'تم تحديث بيانات المدرس بنجاح.');
    }

    /**
     * 4.4.2.3 فصل/تعطيل مدرس من المعهد
     */
    public function destroy($id)
    {
        $teacher = $this->institute()
            ->teachers()
            ->findOrFail($id);

        // نفصل الربط من الجدول المحوري فقط
        $teacher->institutes()->detach($this->institute()->id);

        return redirect()
            ->route('manager.teachers.index')
            ->with('success', 'تم تعطيل المدرس بنجاح.');
    }
}
