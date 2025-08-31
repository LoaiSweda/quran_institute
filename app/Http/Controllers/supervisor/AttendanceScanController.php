<?php
// app/Http/Controllers/supervisor/AttendanceScanController.php

namespace App\Http\Controllers\supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionSchedule;
use Carbon\Carbon;

class AttendanceScanController extends Controller
{
    public function show(Request $request)
    {
        $supervisor = Auth::user();

        // خُذ كل المعاهد المرتبط بها المستخدم (بدون تقييد الدور حتى لا تصفّر)
        $instIds = $supervisor->institutes()->pluck('institutes.id')->toArray();

        // إن لم يكن مرتبطًا بأي معهد، أوقف برسالة واضحة
        if (empty($instIds)) {
            abort(403, 'حسابك غير مرتبط بأي معهد.');
        }

        // اسم اليوم بالإنجليزية مثل Sunday
        $today = Carbon::now('Asia/Damascus')->format('l');

        // الاستعلام: طابق اليوم بلا حساسية حالة الأحرف/المسافات، وفلترة بالـ relations
        $schedules = SessionSchedule::query()
            ->with(['educationClass' => function ($q) {
                $q->select('id','name','subject_id');
            }])
            ->whereRaw('LOWER(TRIM(day_of_week)) = ?', [strtolower($today)])
            ->whereHas('educationClass.subject', function ($q) use ($instIds) {
                $q->whereIn('institute_id', $instIds);
            })
            ->orderBy('start_time')
            ->get();

        return view('attendance.scan', compact('schedules'));
    }
}
