<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Teacher extends Model
{

    /**
     * الحقول القابلة للملء جماعياً.
     */
    protected $fillable = [
        'image',
        'first_name',
        'last_name',
        'phone',
        'address',
        'birthdate',
        'user_id',
    ];

    /**
     * عمل cast لحقل birthdate ليُرجع كائن Carbon.
     */
    protected $casts = [
        'birthdate'   => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
    protected $table = 'teachers';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false; // إذا لم تستخدم timestamps في teachers



    /**
     * علاقة Teacher ⇄ User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة Teacher ⇄ Institute عبر pivot institute_user.
     * user_id في pivot يربط إلى teacher.user_id
     * institute_id يربط إلى المعهد.
     */
    public function institutes()
    {
        return $this->belongsToMany(
            Institute::class,
            'institute_user',
            'user_id',      // اسم العمود في جدول pivot الذي يشير لـ هذا النموذج
            'institute_id', // اسم العمود في جدول pivot الذي يشير للمعهد
            'user_id',      // ***هذا هو عمود الـ localKey في this model***
            'id'            // عمود المفتاح في جدول Institute (عادة id)
        )
            ->withPivot('role_institute')
            ->wherePivot('role_institute','teacher')
            ->withTimestamps();
    }

    /**
     * (اختياري) إذا أردت في المستقبل عرض حلقات Teacher،
     * أنشئ موديل TeachingAssignment واضبط الجدول،
     * ثم فكّ تعليق هذا الكود:
     */
    // public function classes()
    // {
    //     return $this->hasMany(TeachingAssignment::class, 'teacher_id');
    // }
    public function classes()
    {
        return $this->belongsToMany(
            \App\Models\EducationClass::class,
            'users_classes',
            'user_id',
            'class_id'
        )->withTimestamps();
    }

    /**
     * (اختياري) إذا أردت جدولاً أسبوعياً،
     * أنشئ موديل WeeklySchedule واضبط الجدول،
     * ثم فكّ تعليق هذا الكود:
     */
    // public function schedule()
    // {
    //     return $this->hasMany(WeeklySchedule::class, 'teacher_id');
    // }
}
