<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SessionSchedule;
use App\Models\Persent;
use App\Models\UserPersent;
use App\Models\StudentProgress;
use App\Models\User;
use App\Models\EducationClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AttendanceController extends Controller
{
    /**
     * Scan a student's QR and register attendance.
     */
    public function scan(Request $req)
    {
        $req->validate([
            'qr'                  => 'required|string',
            'session_schedule_id' => 'required|exists:session_schedules,id',
        ]);

        // Find the student by QR
        $student = Student::where('qr', $req->qr)->firstOrFail();

        // Find the schedule
        $schedule = SessionSchedule::findOrFail($req->session_schedule_id);

        // Check if today is the correct day for this session
        $today = Carbon::now()->dayOfWeekIso;  // 1=Mon … 7=Sun
        if ($schedule->day_of_week != $today) {
            return response()->json(['message' => 'ليست هذه الحصة اليوم'], 403);
        }

        // Check if the current time is within the session's time
        $now = Carbon::now('Asia/Damascus')->format('H:i');  // Syria Time
        if ($now < $schedule->start_time->format('H:i') || $now > $schedule->end_time->format('H:i')) {
            return response()->json(['message' => 'ليست ضمن وقت الحصة'], 403);
        }

        // Check if the student is already marked present for this schedule
        $already = $schedule->users()
            ->where('users.id', $student->user_id)
            ->exists();

        if ($already) {
            return response()->json(['message' => 'هذا الطالب قد سجّل حضوره مسبقاً'], 200);
        }

        // Register the student in the session_users pivot table
        $schedule->users()->attach($student->user_id);


        // Record attendance in users_persents
        $persent = Persent::firstOrCreate([
            'date' => now()->toDateString(),
            'time' => now()->toTimeString()
        ]);

        UserPersent::updateOrCreate(
            [
                'user_id'    => $student->user_id,
                'class_id'   => $schedule->class_id,
                'persent_id' => $persent->id,
            ],
            ['status' => 'present']
        );

        // Update student progress
        $progress = StudentProgress::firstOrCreate(
            [
                'student_id' => $student->id,
                'class_id'   => $schedule->class_id,
            ],
            [
                'number_sessions_attended' => 0,
                'eohservation_rate'        => 0,
                'degree_avg'               => 0,
                'total_points_subject'     => 0,
            ]
        );

        $progress->increment('number_sessions_attended');
        $progress->update([
            'eohservation_rate' => round(
                $progress->number_sessions_attended
                / $schedule->educationClass->session_count
                * 100
            ),
        ]);

        return response()->json(['message' => 'تمّ تسجيل حضور الطالب بنجاح'], 200);
    }

    public function markAbsent(Request $request)
{
    $request->validate([
        'session_schedule_id' => 'required|exists:session_schedules,id',
    ]);

    $schedule = SessionSchedule::findOrFail($request->session_schedule_id);
    $classId = $schedule->class_id;
    $today = now()->toDateString();
    
    // Record attendance in users_persents
    $persent = Persent::firstOrCreate([
        'date' => now()->toDateString(),
        'time' => now()->toTimeString()
    ]);

    // 1. الحصول على جميع الطلاب المسجلين في الحلقة
    $classStudents = EducationClass::findOrFail($classId)
        ->enrolledStudents()
        ->get();

    // 2. الحصول على الطلاب الذين حضروا (سجلوا عبر QR)
    $presentStudents = $schedule->users()
        ->pluck('users.id')
        ->toArray();

    $absentCount = 0;
    
    foreach ($classStudents as $user) {
        if (!in_array($user->id, $presentStudents)) {
            UserPersent::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'class_id' => $classId,
                    'persent_id' => $persent->id,
                ],
                ['status' => 'absent']
            );
            $absentCount++;
        }
    }

    return response()->json([
        'message' => "تم تسجيل $absentCount طالب كغائبين بنجاح"
    ], 200);
}
}