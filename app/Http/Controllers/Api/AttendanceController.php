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

        // 1. تأكد أن اليوم صحيح
        $today = Carbon::now('Asia/Damascus')->dayOfWeek;  // 0=الأحد … 6=السبت
        if ($schedule->day_of_week != $today) {
            return response()->json(['message' => 'ليست هذه الحصة اليوم'], 403);
        }

        // 2. تأكد أن الوقت ضمن وقت الحصة
        $now = Carbon::now('Asia/Damascus')->format('H:i');
        if ($now < $schedule->start_time->format('H:i')
            || $now > $schedule->end_time->format('H:i')) {
            return response()->json(
                ['message' => 'ليست ضمن وقت الحصة'],
                403,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        // 3. أنشئ أو احصل على سجل حضور خاص بهذه الجلسة والموعد
        $persent = Persent::firstOrCreate(
            [
                'date'                 => now()->toDateString(),
                'session_schedule_id'  => $schedule->id,
            ],
            [
                'time' => now()->toTimeString(),
            ]
        );


        // إذا وُجد للتوّ (الجلسة بدأت للتوّ)، زدّ session_count
        if ($persent->wasRecentlyCreated) {
            // الرابط من schedule إلى class ثم التحديث
            $class = $schedule->educationClass;
            $class->increment('session_count');
        }

        // 4. تحقق من عدم تسجيل الطالب مسبقاً في هذه الجلسة
        $already = UserPersent::where([
            'user_id'    => $student->user_id,
            'class_id'   => $schedule->class_id,
            'persent_id' => $persent->id,
        ])->exists();

        if ($already) {
            return response()->json([
                'message' => 'هذا الطالب قد سجّل حضوره مسبقاً في هذه الجلسة'
            ], 200);
        }

        // 5. سجل الحضور في users_persents
        UserPersent::create([
            'user_id'    => $student->user_id,
            'class_id'   => $schedule->class_id,
            'persent_id' => $persent->id,
            'status'     => 'present',
        ]);

        // 6. حدّث تقدم الطالب
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

        $totalSessions = $schedule
            ->educationClass
            ->subject
            ->total_sessions;

        $progress->increment('number_sessions_attended');
        $progress->update([
            'eohservation_rate' => round(
                $progress->number_sessions_attended
                / $totalSessions
                * 100
            ),
        ]);

        return response()->json([
            'message' => 'تمّ تسجيل حضور الطالب بنجاح'
        ], 200);
    }

    public function markAbsent(Request $request)
    {
        $request->validate([
            'session_schedule_id' => 'required|exists:session_schedules,id',
        ]);

        $schedule = SessionSchedule::findOrFail($request->session_schedule_id);

        // التحقق مما إذا تم تسجيل الغياب مسبقاً
        if ($schedule->isAttendanceMarked()) {
            return response()->json([
                'message' => 'لقد قمت بتسجيل الغياب لهذه الجلسة مسبقاً'
            ], 400);
        }

        // التحقق من انتهاء وقت الحصة
        if (! $schedule->isSessionEnded()) {
            return response()->json([
                'message' => 'لا يمكن تسجيل الغياب قبل نهاية الحصة'
            ], 400);
        }

        $classId = $schedule->class_id;
        $today   = now()->toDateString();

        // إنشاء أو استرجاع سجل Persent المخصَّص لهذه الجلسة واليوم
        $persent = Persent::firstOrCreate(
            [
                'date'                 => $today,
                'session_schedule_id'  => $schedule->id,
            ],
            [
                'time' => now()->toTimeString(),
            ]
        );

        // 1. جلب جميع الطلاب المسجلين في الحلقة
        $classStudents = EducationClass::findOrFail($classId)
            ->enrolledStudents()
            ->pluck('id')  // افترضنا أن enrolledStudents ترجع علاقة users
            ->toArray();

        // 2. جلب الطلاب الذين سجلوا حضوراً لهذه الجلسة (من users_persents)
        $presentStudents = UserPersent::where('persent_id', $persent->id)
            ->pluck('user_id')
            ->toArray();

        $absentCount = 0;

        // 3. وضع علامة "absent" لكل من لم يحضر
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

        // 4. وضع علامة أن الغياب تم تسجيله (مثلاً: تحديث حقل في الجلسة)
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