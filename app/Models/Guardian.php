<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = ['user_id','phone','firstname','lastname','address'];

    public function getNameAttribute(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
