<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'subject_id',         // ← أضفه هنا
        'user_id',
        'students_count',
        'session_count',
        'qr',
        'present_percentage',
    ];
//    public function teacher()
//    {
//        return $this->belongsTo(User::class, 'user_id');
//    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class,
            'user_id',   // FK في جدول classes
            'user_id'    // PK في جدول teachers
        );

    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_classes', 'class_id', 'user_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    public function exams()
    {
        return $this->hasMany(Exam::class, 'class_id');
    }
    public function sessions()
    {
        return $this->hasMany(SessionSchedule::class, 'class_id');
    }
    public function progress()
    {
        return $this->hasMany(StudentProgress::class, 'class_id');
    }

}
