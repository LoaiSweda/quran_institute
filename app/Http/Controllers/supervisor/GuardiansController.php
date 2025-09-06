<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Institute;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuardiansController extends Controller
{
    protected function myInstituteIds(): array
    {
        return auth()->user()
            ? auth()->user()->institutes()->pluck('institutes.id')->toArray()
            : [];
    }


    protected function currentInstitute(Request $request): Institute
    {
        $ids = $this->myInstituteIds();
        abort_if(empty($ids), 403, 'لا تملك صلاحية على أي معهد.');

        if (count($ids) === 1) {
            return Institute::findOrFail($ids[0]);
        }

        $iid = (int) $request->query('institute_id');
        abort_if(!$iid, 422, 'يرجى تحديد المعهد عبر المعامل ?institute_id=');
        abort_if(!in_array($iid, $ids, true), 403, 'المعهد المحدد غير مصرح به.');
        return Institute::findOrFail($iid);
    }

    protected function guardiansQueryForInstitute(int $instituteId)
    {
        return Guardian::query()
            ->with(['students','user'])
            ->whereHas('user.institutes', fn($q) => $q->where('institute_id', $instituteId));
    }

    public function index(Request $request)
    {
        $inst  = $this->currentInstitute($request);
        $query = $this->guardiansQueryForInstitute($inst->id);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname',  'like', "%{$search}%")
                    ->orWhere('phone',     'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$search}%"));
            });
        }

        if ($sort = $request->input('sort')) {
            $parts = explode('_', $sort);
            $field = $parts[0] ?? 'created_at';
            $dir   = (isset($parts[1]) && strtolower($parts[1]) === 'asc') ? 'asc' : 'desc';
            $query->orderBy($field, $dir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $guardians = $query->paginate(15)->withQueryString();

        return view('supervisor.guardians.index', compact('guardians','inst'));
    }

    public function create(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $guardians = $this->guardiansQueryForInstitute($inst->id)
            ->orderBy('created_at','desc')
            ->paginate(10)
            ->withQueryString();

        return view('supervisor.guardians.create', compact('guardians','inst'));
    }

    public function store(Request $request)
    {
        $inst = $this->currentInstitute($request);

        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'address'   => 'nullable|string|max:500',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|confirmed|min:6',
        ]);

        $guardianRoleId = Role::where('name', 'guardian')->value('id') ?? 6;

        DB::transaction(function () use ($data, $inst, $guardianRoleId) {

            $user = User::create([
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id'  => $guardianRoleId,
            ]);

            Guardian::create([
                'user_id'   => $user->id,
                'firstname' => $data['firstname'],
                'lastname'  => $data['lastname'],
                'phone'     => $data['phone'],
                'address'   => $data['address'] ?? null,
            ]);

            $user->institutes()->sync([
                $inst->id => ['role_institute' => 'guardian']
            ]);
        });

        return redirect()
            ->route('admin.guardians.index', ['institute_id' => $inst->id])
            ->with('success', 'تم إنشاء حساب وليّ الأمر وربطه بالمعهد الحالي فقط.');
    }

    public function show(Request $request, Guardian $guardian)
    {
        $inst = $this->currentInstitute($request);

        abort_unless(
            $guardian->user()->whereHas('institutes', fn($q) => $q->where('institute_id', $inst->id))->exists(),
            403, 'لا تملك صلاحية على هذا الحساب.'
        );

        $guardian->load(['students','user']);
        return view('supervisor.guardians.show', compact('guardian','inst'));
    }

    public function edit(Request $request, Guardian $guardian)
    {
        $inst = $this->currentInstitute($request);

        abort_unless(
            $guardian->user()->whereHas('institutes', fn($q) => $q->where('institute_id', $inst->id))->exists(),
            403, 'لا تملك صلاحية على هذا الحساب.'
        );

        $guardian->load('user');
        return view('supervisor.guardians.edit', compact('guardian','inst'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $inst = $this->currentInstitute($request);

        abort_unless(
            $guardian->user()->whereHas('institutes', fn($q) => $q->where('institute_id', $inst->id))->exists(),
            403, 'لا تملك صلاحية على هذا الحساب.'
        );

        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'address'   => 'nullable|string|max:500',
            'email'     => 'required|email|unique:users,email,' . $guardian->user_id,
            'password'  => 'nullable|confirmed|min:6',
        ]);

        DB::transaction(function () use ($guardian, $data) {
            $guardian->update([
                'firstname' => $data['firstname'],
                'lastname'  => $data['lastname'],
                'phone'     => $data['phone'],
                'address'   => $data['address'] ?? null,
            ]);

            $user = $guardian->user;
            $user->email = $data['email'];
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->save();
        });

        return redirect()
            ->route('admin.guardians.index', request()->only('institute_id'))
            ->with('success', 'تم تحديث بيانات وليّ الأمر بنجاح.');
    }

    public function destroy(Request $request, Guardian $guardian)
    {
        $inst = $this->currentInstitute($request);

        abort_unless(
            $guardian->user()->whereHas('institutes', fn($q) => $q->where('institute_id', $inst->id))->exists(),
            403, 'لا تملك صلاحية على هذا الحساب.'
        );

        DB::transaction(function () use ($guardian, $inst) {
            $guardian->user->institutes()->detach($inst->id);
            $user = $guardian->user;
            $guardian->delete();
            $user->delete();
        });

        return redirect()
            ->route('admin.guardians.index', ['institute_id' => $inst->id])
            ->with('success', 'تم حذف وليّ الأمر.');
    }
}
