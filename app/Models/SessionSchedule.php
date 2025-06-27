<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionSchedule extends Model
{
    protected $table = 'session_schedules';
    protected $fillable = [
        'user_id','class_id','day_of_week','start_time','end_time'
    ];

    public function educationClass()
    {
        return $this->belongsTo(EducationClass::class, 'class_id');
    }
}
