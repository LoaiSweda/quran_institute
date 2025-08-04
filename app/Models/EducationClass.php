<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'subject_id',         
        'user_id',
        'students_count',
        'session_count',
        'qr',
        'present_percentage',
    ];

    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class,
            'user_id',   // FK in classes
            'user_id'    // PK in teachers
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
    public function sessionSchedules()
    {
        return $this->hasMany(SessionSchedule::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'users_classes', 'class_id', 'user_id')
            ->withPivot('created_at', 'updated_at');
    }

    public function enrolledUsers()
{
    return $this->belongsToMany(
        User::class,
        'users_classes',
        'class_id',
        'user_id'
    );
}

public function enrolledStudents()
{
    return $this->enrolledUsers()
        ->whereHas('student') 
        ->with('student');
}
}
