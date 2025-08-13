<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPersent extends Model
{
    protected $table = 'users_persents';
    public $incrementing = false;
    protected $fillable = ['user_id','class_id','persent_id','status'];

     public function persent()
    {
        return $this->belongsTo(Persent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

