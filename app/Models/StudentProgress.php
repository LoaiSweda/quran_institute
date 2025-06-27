<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    protected $table = 'student_progress';
    protected $fillable = [
        'eohservation_rate','degree_avg','number_sessions_attended','total_points_subject','class_id','student_id'
    ];

    public function educationClass()
    {
        return $this->belongsTo(EducationClass::class, 'class_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

