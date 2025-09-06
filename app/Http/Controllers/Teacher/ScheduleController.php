<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SessionSchedule;

class ScheduleController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $sessions = SessionSchedule::query()
            ->where('user_id', $userId)
            ->with('educationClass')        
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        if ($sessions->isEmpty()) {
            $sessionsByDay = collect();
            $timeSlots = collect(range(8, 22))->map(fn($h) => sprintf('%02d:00', $h));
        } else {
            $map = [
                'Sunday'    => 0,
                'Monday'    => 1,
                'Tuesday'   => 2,
                'Wednesday' => 3,
                'Thursday'  => 4,
                'Friday'    => 5,
                'Saturday'  => 6,
            ];

            $sessionsByDay = $sessions->groupBy(function ($s) use ($map) {
                return $map[$s->day_of_week] ?? null;
            });

            $minHour = (int) $sessions->min(fn($s) => (int) $s->start_time->format('H'));
            $maxHour = (int) $sessions->max(fn($s) => (int) $s->end_time->format('H'));

            if ($minHour === 0 && $maxHour === 0) {
                $minHour = 8; $maxHour = 22;
            }

            $timeSlots = collect(range($minHour, $maxHour))
                ->map(fn($h) => sprintf('%02d:00', $h));
        }

        $days = [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];

        return view('teacher.schedule.index', compact(
            'sessionsByDay',
            'days',
            'timeSlots'
        ));
    }
}
