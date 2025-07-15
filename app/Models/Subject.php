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
        'institute_id','exams_count',
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
}
