<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name','description','start_date','institute_id',
        'end_date','is_active','exams_count','level','degree','total_sessions'
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

