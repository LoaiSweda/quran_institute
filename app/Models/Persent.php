<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persent extends Model
{
    protected $fillable = ['date', 'time'];
    
    protected $casts = [
        'date' => 'date:Y-m-d',
        'time' => 'datetime:H:i:s'
    ];

    public function userPersents()
    {
        return $this->hasMany(UserPersent::class);
    }
}