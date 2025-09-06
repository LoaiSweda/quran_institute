<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Institute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'address', 'image', 'user_id','institute_stamp_path','director_signature_path','phone','email'
    ];

    public function getInstituteStampUrlAttribute(): ?string
    {
        return $this->institute_stamp_path
            ? Storage::disk('public')->url($this->institute_stamp_path)
            : null;
    }
    public function getDirectorSignatureUrlAttribute(): ?string
    {
        return $this->director_signature_path
            ? \Storage::disk('public')->url($this->director_signature_path)
            : null;
    }


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

    // علاقة مباشرة مع الطلاب من خلال الجدول الوسيط institute_user
    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'institute_user',
            'institute_id',
            'user_id',
            'id',
            'user_id'
        )->wherePivot('role_institute', 'student');
    }

    // علاقة مع مستخدمين الطلاب
    public function studentUsers()
    {
        return $this->belongsToMany(
            User::class,
            'institute_user',
            'institute_id',
            'user_id'
        )->wherePivot('role_institute', 'student');
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
            ->wherePivot('role_institute', 'teacher')
            ->withTimestamps();
    }
    public function classesCount()
    {
        return $this->hasManyThrough(EducationClass::class, Subject::class)->count();
    }

    public function getClassesCountAttribute()
    {
        if (!$this->relationLoaded('subjects')) {
            $this->load('subjects');
        }

        return $this->subjects->sum(function($subject) {
            return $subject->educationClasses->count();
        });
    }
}
