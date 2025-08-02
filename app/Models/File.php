<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class File extends Model
{
    protected $fillable = ['name','path','size','mime'];

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
