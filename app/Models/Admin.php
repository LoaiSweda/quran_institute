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

     protected $casts = [
        'birthdate' => 'date', // يجعل القيمة ترجع كـ Carbon instance
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


