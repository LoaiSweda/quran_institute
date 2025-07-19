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
//    public function users()
//    {
//        return $this->belongsToMany(User::class, 'institute_user')
//            ->withPivot('role_institute')
//            ->withTimestamps();
//    }
// app/Models/Institute.php
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'institute_user',
            'institute_id',
            'user_id'
        )->withPivot('role_institute')->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(
            Teacher::class,      // الموديل المرتبط
            'institute_user',    // اسم جدول pivot
            'institute_id',      // pivot FK للإينستيتيوت
            'user_id',           // pivot FK للتيتشر (ولكن هذه القيمة يجب أن تقرأ من teacher.user_id)
            'id',                // parentKey في Institute (عادة 'id')
            'user_id'            // relatedKey في Teacher (ليس 'id' بل عمود user_id)
        )
            ->withPivot('role_institute')
            ->wherePivot('role_institute','teacher')
            ->withTimestamps();
    }

}


