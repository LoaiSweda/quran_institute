<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','image'];


    public function libraries()
    {
        return $this->hasMany(Library::class, 'category_id');
    }
}

