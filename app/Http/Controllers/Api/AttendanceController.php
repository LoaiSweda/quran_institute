<?php

// app/Http/Controllers/Api/AttendanceController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SessionSchedule;
use App\Models\Persent;
use App\Models\UserPersent;
use App\Models\StudentProgress;

class AttendanceController extends Controller
{
    public function scan(Request $req)
    {
        $req->validate([
            'qr'                  => 'required|string',
            'session_schedule_id' => 'required|exists:session_schedules,id',
        ]);

        // 1) إيجاد الطالب
        $student = Student::where('qr',$req->qr)->firstOrFail();

        // 2) إيجاد الحصة
        $schedule = SessionSchedule::findOrFail($req->session_schedule_id);

        // (اختياري) تأكد اليوم والوقت ضمن نافذة الحصة
        $today = now()->dayOfWeekIso;  // 1=Mon … 7=Sun
        if ($schedule->day_of_week != $today) {
            return response()->json(['message'=>'ليست هذه الحصة اليوم'],403);
        }

        $now = now()->format('H:i');
        if ($now < $schedule->start_time->format('H:i') ||
            $now > $schedule->end_time->format('H:i')) {
            return response()->json(['message'=>'ليست ضمن وقت الحصة'],403);
        }

        // 3) سجّل الحضور في pivot session_users
        $schedule->users()->syncWithoutDetaching($student->user_id);

        // 4) سجّل الحالة في users_persents
        $persent = Persent::firstOrCreate(['date'=>now()->toDateString()]);
        UserPersent::updateOrCreate(
          [
            'user_id'    => $student->user_id,
            'class_id'   => $schedule->class_id,
            'persent_id' => $persent->id,
          ],
          ['status'=>'present']
        );

        // 5) حدِّث student_progress
        $progress = StudentProgress::firstOrCreate([
            'student_id'=>$student->id,
            'class_id'  =>$schedule->class_id,
        ],[
            'number_sessions_attended'=>0,
            'eohservation_rate'=>0,
            'degree_avg'=>0,
            'total_points_subject'=>0,
        ]);
        $progress->increment('number_sessions_attended');
        $progress->update([
            'eohservation_rate' => round(
                $progress->number_sessions_attended /
                $schedule->educationClass->session_count
                * 100
            ),
        ]);

        return response()->json(['message'=>'تمّ تسجيل حضور الطالب بنجاح']);
    }
}
