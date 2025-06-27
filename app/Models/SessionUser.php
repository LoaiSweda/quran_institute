<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionUser extends Model
{
    protected $table = 'session_users';
    protected $fillable = ['user_id','session_schedule_id'];
}
