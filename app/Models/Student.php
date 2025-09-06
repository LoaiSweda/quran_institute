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
            EducationClass::class, 
            'users_classes',      
            'user_id',             
            'class_id',            
            'user_id',             
            'id'                   
        )->withTimestamps();
    }

}

