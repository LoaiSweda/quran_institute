<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'image','first_name','last_name','phone','address','birthdate','user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
