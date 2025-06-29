<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SessionSchedule;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * عرض الجدول الأسبوعي للأستاذ
     */
    public function index()
    {
        // جلب جميع الجلسات الخاصة بالأستاذ المصادق عليه مع بيانات الصف
        $sessions = auth()->user()
                         ->sessionSchedules()
                         ->with('educationClass')
                         ->get();

        // تجميع الجلسات حسب رقم اليوم (0=السبت … 6=الجمعة)
        $sessionsByDay = $sessions->groupBy('day_of_week');

        // استخراج ساعتي البداية والنهاية لأقصى وأدنى وقت
        $hours = $sessions
            ->pluck('start_time')
            ->merge($sessions->pluck('end_time'))
            ->map(function ($t) {
                // نحول "HH:MM:SS" إلى رقم الساعة
                return (int) substr($t, 0, 2);
            });

        $minHour = $hours->min() ?: 8;  // افتراضياً من 8 صباحاً
        $maxHour = $hours->max() ?: 22; // افتراضياً إلى 5 مساءً

        // إنشاء مصفوفة أوقات كل ساعة بين الحدين
        $timeSlots = collect(range($minHour, $maxHour))
            ->map(fn($h) => sprintf('%02d:00', $h));

        // أسماء الأيام بالعربي
        $days = [
            0 => 'السبت',
            1 => 'الأحد',
            2 => 'الاثنين',
            3 => 'الثلاثاء',
            4 => 'الأربعاء',
            5 => 'الخميس',
            6 => 'الجمعة',
        ];

        // تمرير البيانات إلى صفحة العرض
        return view('teacher.schedule.index', compact(
            'sessionsByDay',
            'days',
            'timeSlots'
        ));
    }
}
