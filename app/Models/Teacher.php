<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Teacher extends Model
{

    protected $fillable = [
        'image',
        'first_name',
        'last_name',
        'phone',
        'address',
        'birthdate',
        'user_id',
    ];


    protected $casts = [
        'birthdate'   => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
    protected $table = 'teachers';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function institutes()
    {
        return $this->belongsToMany(
            Institute::class,
            'institute_user',
            'user_id',
            'institute_id',
            'user_id',
            'id'
        )
            ->withPivot('role_institute')
            ->wherePivot('role_institute','teacher')
            ->withTimestamps();
    }

    public function classes()
    {
        return $this->belongsToMany(
            \App\Models\EducationClass::class,
            'users_classes',
            'user_id',
            'class_id'
        )->withTimestamps();
    }
    public function teachingClasses()
    {
        return $this->hasMany(\App\Models\EducationClass::class, 'user_id', 'user_id');
    }



}
