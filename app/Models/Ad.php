<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $table = 'ads';
    protected $fillable = [
        'title','description','link','end_date','user_id','type_id','status'
    ];
}
