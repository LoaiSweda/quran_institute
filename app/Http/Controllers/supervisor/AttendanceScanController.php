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

        // اجلب المعاهد التي هو مشرف لها
        $instIds = $supervisor
            ->institutes()
            ->wherePivot('role_institute','supervisor')
            ->pluck('institutes.id')
            ->toArray();

        // رقم اليوم الحالي: 0=الأحد … 6=السبت
        $today = Carbon::now('Asia/Damascus')->dayOfWeek;

        // جلب جداول الحصص التي يومها اليوم الحالي
        $schedules = SessionSchedule::with('educationClass')
            ->whereIn('class_id', function($q) use($instIds) {
                $q->select('classes.id')
                ->from('classes')
                ->join('subjects','classes.subject_id','=','subjects.id')
                ->whereIn('subjects.institute_id',$instIds);
            })
            ->where('day_of_week', $today)
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->orderBy('start_time')
            ->get();

        return view('attendance.scan', compact('schedules'));
    }
}
