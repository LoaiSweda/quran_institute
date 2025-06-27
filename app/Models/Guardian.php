<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = ['phone','firstname','lastname','address'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
