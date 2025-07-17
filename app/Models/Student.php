<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'image','first_name','last_name','guardian_id','qr','phone',
        'address','birthdate','father_name','points','user_id','present_percentage'
    ];
    protected $casts = [
        'birthdate' => 'date:Y-m-d',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function exams()
    {
        return $this->hasMany(\App\Models\Exam::class, 'student_id');
    }
    public function classes()
    {
        return $this->belongsToMany(
            \App\Models\EducationClass::class,
            'users_classes',   // اسم pivot table
            'user_id',         // العمود في users_classes الذي يربط للموديل Student (student->user_id)
            'class_id',        // العمود في users_classes الذي يربط للحلقة
            'user_id',         // المفتاح المحلي في جدول students
            'id'               // المفتاح في جدول classes
        )->withTimestamps();
    }

}

