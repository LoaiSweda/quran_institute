<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionSchedule extends Model
{
    protected $table = 'session_schedules';

    protected $fillable = [
        'user_id','class_id','day_of_week','start_time','end_time'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    /** أسماء الأيام باللغتين */
    public const DAYS_AR = [
        0 => 'الأحد',
        1 => 'الإثنين',
        2 => 'الثلاثاء',
        3 => 'الأربعاء',
        4 => 'الخميس',
        5 => 'الجمعة',
        6 => 'السبت',
    ];

    public const DAYS_EN = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
    ];

    public const DAYS_EN_SHORT = [
        0 => 'sun',
        1 => 'mon',
        2 => 'tue',
        3 => 'wed',
        4 => 'thu',
        5 => 'fri',
        6 => 'sat',
    ];

    /** ترجيع id الحلقة */
    public function educationClass()
    {
        return $this->belongsTo(EducationClass::class, 'class_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'session_users',
            'session_schedule_id',
            'user_id',
        )->withTimestamps();
    }

    public function persents()
    {
        return $this->hasMany(Persent::class, 'session_schedule_id');
    }

    /** --- أدوات تحويل/عرض اليوم --- */

    /** يحوّل أي قيمة لرقم اليوم 0..6 إن أمكن */
    public static function normalizeDow($value): ?int
    {
        if ($value === null) return null;

        // رقم مباشر
        if (is_int($value) || ctype_digit((string) $value)) {
            $i = (int) $value;
            return ($i >= 0 && $i <= 6) ? $i : null;
        }

        $key = mb_strtolower(trim((string) $value), 'UTF-8');

        // دعم العربية الشائعة
        $arMap = [
            'الأحد' => 0, 'الاحد' => 0,
            'الاثنين' => 1, 'الإثنين' => 1,
            'الثلاثاء' => 2,
            'الأربعاء' => 3, 'الاربعاء' => 3,
            'الخميس' => 4,
            'الجمعة' => 5,
            'السبت' => 6,
        ];
        if (isset($arMap[$key])) return $arMap[$key];

        $map = [
            '0' => 0, 'sunday' => 0, 'sun' => 0,
            '1' => 1, 'monday' => 1, 'mon' => 1,
            '2' => 2, 'tuesday' => 2, 'tue' => 2, 'tues' => 2,
            '3' => 3, 'wednesday' => 3, 'wed' => 3,
            '4' => 4, 'thursday' => 4, 'thu' => 4, 'thur' => 4, 'thurs' => 4,
            '5' => 5, 'friday' => 5, 'fri' => 5,
            '6' => 6, 'saturday' => 6, 'sat' => 6,
        ];

        return $map[$key] ?? null;
    }

    /** يرجع رقم اليوم 0..6 بغض النظر عن طريقة التخزين */
    public function getDayOfWeekIndexAttribute(): ?int
    {
        $raw = $this->attributes['day_of_week'] ?? null;
        return self::normalizeDow($raw);
    }

    /** اسم اليوم بالعربية */
    public function getDayOfWeekNameArAttribute(): ?string
    {
        $i = $this->day_of_week_index;
        return $i === null ? null : self::DAYS_AR[$i];
    }

    /** اسم اليوم بالإنجليزية الكاملة */
    public function getDayOfWeekNameEnAttribute(): ?string
    {
        $i = $this->day_of_week_index;
        return $i === null ? null : self::DAYS_EN[$i];
    }

    /** سكوب ترتيب صحيح للأسبوع حتى لو كانت القيم نصية */
    public function scopeOrderByDow($q, string $dir = 'asc')
    {
        $case = "
            CASE
                WHEN LOWER(day_of_week) IN ('0','sunday','sun') THEN 0
                WHEN LOWER(day_of_week) IN ('1','monday','mon') THEN 1
                WHEN LOWER(day_of_week) IN ('2','tuesday','tue','tues') THEN 2
                WHEN LOWER(day_of_week) IN ('3','wednesday','wed') THEN 3
                WHEN LOWER(day_of_week) IN ('4','thursday','thu','thur','thurs') THEN 4
                WHEN LOWER(day_of_week) IN ('5','friday','fri') THEN 5
                WHEN LOWER(day_of_week) IN ('6','saturday','sat') THEN 6
                ELSE 7
            END
        ";
        return $q->orderByRaw("$case $dir");
    }

    /** سكوب فلترة اليوم: يقبل رقم/إنجليزي/عربي */
    public function scopeWhereDow($q, $value)
    {
        $i = self::normalizeDow($value);
        if ($i === null) return $q;

        // نجمع كل الصيغ الممكنة لهذا اليوم
        $variants = [
            (string) $i,
            self::DAYS_EN[$i],
            self::DAYS_EN_SHORT[$i],
        ];

        // فلترة مرنة
        return $q->where(function ($qq) use ($variants, $i) {
            $qq->where('day_of_week', $i)
               ->orWhereIn(DB::raw('LOWER(day_of_week)'), array_map('strtolower', $variants));
        });
    }

    /** --- وظائف الحضور والوقت كما كانت --- */

    public function isAttendanceMarked()
    {
        return $this->attendance_marked;
    }

    public function markAttendanceCompleted()
    {
        $this->attendance_marked = true;
        $this->save();
    }

    public function timeRemaining()
    {
        $now = Carbon::now('Asia/Damascus');

        $end = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $now->toDateString() . ' ' . $this->end_time->format('H:i:s'),
            'Asia/Damascus'
        );

        if ($now->gte($end)) {
            return '00:00';
        }

        return $end->diffForHumans(
            $now,
            [
                'syntax'  => Carbon::DIFF_RELATIVE_TO_NOW,
                'parts'   => 2,
                'short'   => true,
                'options' => Carbon::JUST_NOW
            ]
        );
    }

    public function isSessionEnded()
    {
        $now = Carbon::now('Asia/Damascus');

        $end = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $now->toDateString() . ' ' . $this->end_time->format('H:i:s'),
            'Asia/Damascus'
        );

        return $now->gte($end);
    }
}
