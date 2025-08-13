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
        return $this->hasOne(Student::class, 'user_id');
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
        return $this->hasOne(Institute::class, 'user_id');
    }

    public function studentProfile()
    {
        return $this->hasOne(\App\Models\Student::class, 'user_id');
    }

    public function hasRole(string|array $roles): bool
    {
        if (!$this->role) { // If the user has no role assigned
            return false;
        }

if (is_array($roles)) {
    return in_array($this->role->name, $roles);
}

return $this->role->name === $roles;
}

public
function hasAnyRole(array $roles): bool
{
    return $this->hasRole($roles);
}

// You might also want to add direct accessors for role names if frequently used
public
function getRoleNameAttribute(): ?string
{
    return $this->role->name ?? null;
}

public
function getInstituteIdAttribute(): ?int
{
    // Eager load the 'institute' relationship if it's not already loaded
    // This prevents N+1 query problem if you access institute_id multiple times
    if (!$this->relationLoaded('institute')) {
        $this->load('institute');
    }

    return $this->institute->id ?? null;
}

}
