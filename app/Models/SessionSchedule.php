<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
