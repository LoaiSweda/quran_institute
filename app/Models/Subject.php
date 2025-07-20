<?php
// app/Models/Subject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name','description',
        'start_date','end_date',
        'level','degree',
        'total_sessions','is_active',
        'institute_id','exams_count','image',
    ];

    // Add this:
    protected $casts = [
        'start_date'     => 'date',   // now $subject->start_date is a Carbon instance
        'end_date'       => 'date',
        'is_active'      => 'boolean',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
    public function educationClasses()
    {
        return $this->hasMany(EducationClass::class, 'subject_id');
    }
    public function classes()
    {
        return $this->belongsToMany(
            EducationClass::class,
            'users_classes', // أو pivot table المناسب
            'user_id',       // أو student_id إذا استخدمت pivot مستقلّ
            'class_id'
        )->withTimestamps();
    }

     // إضافة accesor لعرض URL الصورة
    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? asset('storage/subjects/' . $this->image)
            : null;
    }

}
