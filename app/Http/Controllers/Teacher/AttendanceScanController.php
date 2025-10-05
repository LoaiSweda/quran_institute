<?php
// app/Http/Controllers/supervisor/AttendanceScanController.php

namespace App\Http\Controllers\Teacher;

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

        $instIds = $supervisor->institutes()->pluck('institutes.id')->toArray();

        if (empty($instIds)) {
            abort(403, 'حسابك غير مرتبط بأي معهد.');
        }

        $today = Carbon::now('Asia/Damascus')->format('l');

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
