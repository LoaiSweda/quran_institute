<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['class_id','student_id','name','notes','points','degree'];

    public function educationClass()
    {
        return $this->belongsTo(EducationClass::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
