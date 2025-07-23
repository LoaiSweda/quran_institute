<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    public function educationClass()
    {
        return $this->belongsTo(EducationClass::class, 'class_id');
    }
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'session_users',         // اسم الجدول الوسيط
            'session_schedule_id',   // FK في جدول pivot إلى session_schedules
            'user_id',               // FK في جدول pivot إلى users
        )->withTimestamps();
    }

    public function isAttendanceMarked()
    {
        return $this->attendance_marked;
    }

    public function markAttendanceCompleted()
    {
        $this->attendance_marked = true;
        $this->save();
    }


    public function persents()
    {
        return $this->hasMany(Persent::class, 'session_schedule_id');
    }


    public function timeRemaining()
    {
        // وقت الآن في دمشق
        $now = Carbon::now('Asia/Damascus');

        // خذ فقط ساعة ونهاية الحصة (H:i:s) وحولها إلى "دمشق" مع يوم اليوم
        $end = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            // نسند تاريخ اليوم مع الوقت المخزن
            $now->toDateString() . ' ' . $this->end_time->format('H:i:s'),
            'Asia/Damascus'
        );

        // لو انتهت الجلسة بالفعل
        if ($now->gte($end)) {
            return '00:00';
        }

        // احسب الفارق في نفس المنطقة
        return $end->diffForHumans(
            $now,
            [
                'syntax' => Carbon::DIFF_RELATIVE_TO_NOW, // "باقي"
                'parts'  => 2,                           // ساعتين ودقيقتين
                'short'  => true,                        // اختصار "1س 40د"
                'options'=> Carbon::JUST_NOW             // تجنب كلمات زائدة
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
