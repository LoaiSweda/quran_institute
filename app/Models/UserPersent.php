<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPersent extends Model
{
    protected $table = 'users_persents';
    public $incrementing = false;
    protected $fillable = ['user_id','class_id','persent_id','status'];
}

