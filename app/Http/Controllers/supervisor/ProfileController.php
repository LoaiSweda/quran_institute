<?php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected function myInstitutes($user)
    {
        return $user->institutes()->withPivot(['role_institute'])->get();
    }

    protected function resolveCurrentInstitute(Request $request)
    {
        $user = $request->user();
        $list = $this->myInstitutes($user);
        abort_if($list->isEmpty(), 403, 'لا تملك صلاحية على أي معهد.');

        if ($list->count() === 1) {
            return [$list, $list->first()];
        }

        $iid = (int) $request->query('institute_id');
        if ($iid) {
            $current = $list->firstWhere('id', $iid);
            abort_if(!$current, 403, 'هذا المعهد غير مرتبط بحسابك.');
            return [$list, $current];
        }

        return [$list, $list->first()];
    }

    public function show(Request $request)
    {
        $user = $request->user();
        [$institutes, $inst] = $this->resolveCurrentInstitute($request);

        return view('supervisor.profile.show', [
            'user'       => $user,
            'inst'       => $inst,
            'institutes' => $institutes,
        ]);
    }
}
