<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institute extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','address','image','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}

