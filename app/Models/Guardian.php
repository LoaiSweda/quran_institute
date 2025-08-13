<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = ['user_id','phone','firstname','lastname','address'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    // App\Models\Guardian.php



    /** اسم كامل موحّد بغض النظر عن اسم الأعمدة */
    public function getNameAttribute(): string
    {
        $first = $this->first_name ?? $this->firstname ?? '';
        $last  = $this->last_name  ?? $this->lastname  ?? '';
        return trim($first . ' ' . $last);
    }

}

