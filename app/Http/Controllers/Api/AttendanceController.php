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

        $student  = Student::where('qr', $req->qr)->firstOrFail();
        $schedule = SessionSchedule::findOrFail($req->session_schedule_id);

        $today = Carbon::now('Asia/Damascus')->format('l'); 

        if ($schedule->day_of_week !== $today) {
            return response()->json(['message' => 'ليست هذه الحصة اليوم'], 403, [], JSON_UNESCAPED_UNICODE);
        }

        $now = Carbon::now('Asia/Damascus')->format('H:i');
        if ($now < $schedule->start_time->format('H:i')
            || $now > $schedule->end_time->format('H:i')) {
            return response()->json(['message' => 'ليست ضمن وقت الحصة'], 403, [], JSON_UNESCAPED_UNICODE);
        }

        $persent = Persent::firstOrCreate(
            [
                'date'                 => now()->toDateString(),
                'session_schedule_id'  => $schedule->id,
            ],
            ['time' => now()->toTimeString()]
        );

        if ($persent->wasRecentlyCreated) {
            $class = $schedule->educationClass;
            $class->increment('session_count');
        }

        $already = UserPersent::where([
            'user_id'    => $student->user_id,
            'class_id'   => $schedule->class_id,
            'persent_id' => $persent->id,
        ])->exists();

        if ($already) {
            return response()->json(['message' => 'هذا الطالب قد سجّل حضوره مسبقاً في هذه الجلسة'], 200, [], JSON_UNESCAPED_UNICODE);
        }

        UserPersent::create([
            'user_id'    => $student->user_id,
            'class_id'   => $schedule->class_id,
            'persent_id' => $persent->id,
            'status'     => 'present',
        ]);

        $progress = StudentProgress::firstOrCreate(
            ['student_id' => $student->id, 'class_id' => $schedule->class_id],
            ['number_sessions_attended' => 0, 'eohservation_rate' => 0, 'degree_avg' => 0, 'total_points_subject' => 0]
        );

        $totalSessions = $schedule->educationClass->subject->total_sessions;
        $progress->increment('number_sessions_attended');
        $progress->update([
            'eohservation_rate' => round($progress->number_sessions_attended / $totalSessions * 100),
        ]);

        return response()->json(['message' => 'تمّ تسجيل حضور الطالب بنجاح'], 200, [], JSON_UNESCAPED_UNICODE);
    }

    

    public function markAbsent(Request $request)
    {
        $request->validate([
            'session_schedule_id' => 'required|exists:session_schedules,id',
        ]);

        $schedule = SessionSchedule::findOrFail($request->session_schedule_id);

        if ($schedule->isAttendanceMarked()) {
            return response()->json([
                'message' => 'لقد قمت بتسجيل الغياب لهذه الجلسة مسبقاً'
            ], 400);
        }

        if (! $schedule->isSessionEnded()) {
            return response()->json([
                'message' => 'لا يمكن تسجيل الغياب قبل نهاية الحصة'
            ], 400);
        }

        $classId = $schedule->class_id;
        $today   = now()->toDateString();

        $persent = Persent::firstOrCreate(
            [
                'date'                 => $today,
                'session_schedule_id'  => $schedule->id,
            ],
            [
                'time' => now()->toTimeString(),
            ]
        );

        // 1. classStudents
        $classStudents = EducationClass::findOrFail($classId)
            ->enrolledStudents()
            ->pluck('id')  
            ->toArray();

        // 2. presentStudents
        $presentStudents = UserPersent::where('persent_id', $persent->id)
            ->pluck('user_id')
            ->toArray();

        $absentCount = 0;

        // 3. mark "absent" 
        foreach ($classStudents as $userId) {
            if (! in_array($userId, $presentStudents, true)) {
                UserPersent::updateOrCreate(
                    [
                        'user_id'    => $userId,
                        'class_id'   => $classId,
                        'persent_id' => $persent->id,
                    ],
                    ['status' => 'absent']
                );
                $absentCount++;
            }
        }

        $schedule->markAttendanceCompleted();

        return response()->json([
            'message' => "تم تسجيل $absentCount طالب كغائبين بنجاح"
        ], 200);
    }

    public function attendanceStatus(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:session_schedules,id'
        ]);

        $schedule = SessionSchedule::findOrFail($request->schedule_id);
        
        return response()->json([
            'marked' => $schedule->isAttendanceMarked()
        ]);
    }

    public function sessionStatus(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:session_schedules,id'
        ]);

        $schedule = SessionSchedule::findOrFail($request->schedule_id);
        
        return response()->json([
            'ended' => $schedule->isSessionEnded(),
            'time_remaining' => $schedule->timeRemaining()
        ]);
    }
}