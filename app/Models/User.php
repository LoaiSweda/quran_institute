<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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

    // الحلقات التي يشارك فيها (للمعلمين أو الطلاب)
    public function educationClasses()
    {
        return $this->belongsToMany(EducationClass::class, 'users_classes', 'user_id', 'class_id');
    }
}
