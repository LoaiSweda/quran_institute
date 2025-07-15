<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'address', 'image', 'user_id',
    ];

    // صاحب المعهد (مدير)
    public function manager()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // علاقة بالحلقات
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function classes()
    {
        return $this->hasManyThrough(EducationClass::class, Subject::class);
    }

    // علاقة بالمشرفين الإضافيين

    public function admins()
    {
        return $this->belongsToMany(
            User::class,
            'institute_user',       // اسم الجدول
            'institute_id',
            'user_id'
        )
            ->withPivot('role_institute')
            ->withTimestamps();
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'institute_user')
            ->withPivot('role_institute')
            ->withTimestamps();
    }
    public function teachers()
    {
        return $this->belongsToMany(
            Teacher::class,        // موديل المدرّس
            'institute_user',      // جدول المحور
            'institute_id',        // المفتاح على هذا الموديل
            'user_id'              // المفتاح على موديل Teacher (user_id)
        )
            ->withPivot('role_institute')
            ->wherePivot('role_institute','teacher')
            ->withTimestamps();
    }

}


