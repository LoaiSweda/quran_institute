<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:teacher']);
    }

    public function show(Request $request)
    {
        $user = $request->user();

        // معاهد المعلّم عبر pivot (institute_user)
        $institutes = $user->institutes()->select('institutes.id','institutes.name','institutes.address','institutes.image')->get();

        // اختيار المعهد الحالي عبر ?institute_id= أو الأول افتراضياً
        $currentInstId = (int) $request->query('institute_id', (int) optional($institutes->first())->id);
        $inst = $currentInstId ? Institute::find($currentInstId) : null;

        // تأكيد أن المختار ضمن معاهد المعلم
        if ($inst && ! $institutes->pluck('id')->contains($inst->id)) {
            abort(403, 'هذا المعهد غير مرتبط بحسابك.');
        }

        return view('teacher.profile.show', [
            'user'       => $user->loadMissing(['role','teacher']),
            'inst'       => $inst,
            'institutes' => $institutes,
        ]);
    }
}
