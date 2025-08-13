<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['email', 'password', 'role_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function educationClasses()
    {
        return $this->belongsToMany(EducationClass::class, 'users_classes', 'user_id', 'class_id');
    }

    public function sessionSchedules()
    {
        return $this->hasMany(\App\Models\SessionSchedule::class, 'user_id');
    }

    public function classes()
    {
        return $this->belongsToMany(
            \App\Models\EducationClass::class,
            'users_classes',
            'user_id',
            'class_id'
        );
    }

    public function institutes()
    {
        return $this->belongsToMany(
            Institute::class,
            'institute_user',
            'user_id',
            'institute_id'
        )->withPivot('role_institute')->withTimestamps();
    }

    public function institute()
    {
        return $this->hasOne(Institute::class,'user_id');
    }

    public function studentProfile()
    {
        return $this->hasOne(\App\Models\Student::class, 'user_id');
    }

}
