<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'image','first_name','last_name','phone','address','birthdate','user_id'
    ];
    protected $casts = [
        'birthdate'   => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


