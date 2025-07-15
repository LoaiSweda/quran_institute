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
        'birthdate' => 'date',
    ];

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
            'user_id',       // المفتاح في جدول institute_user على Teacher
            'institute_id'   // المفتاح في جدول institute_user على Institute
        )
            ->withPivot('role_institute')
            ->wherePivot('role_institute', 'teacher')
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
