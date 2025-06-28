<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $table = 'libraries';
    protected $fillable = [
        'name','author','description','category_id','file_id','isbn'
    ];
}
