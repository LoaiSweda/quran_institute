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

    public function manager()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function classes()
    {
        return $this->hasManyThrough(EducationClass::class, Subject::class);
    }


    public function admins()
    {
        return $this->belongsToMany(
            User::class,
            'institute_user',       
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
            Teacher::class,      
            'institute_user',    
            'institute_id',      
            'user_id',           
            'id',                
            'user_id'            
        )
            ->withPivot('role_institute')
            ->wherePivot('role_institute','teacher')
            ->withTimestamps();
    }

}


