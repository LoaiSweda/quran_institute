<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $fillable = [
        'image','first_name','last_name','phone','address','birthdate','user_id','institution_stamp_path'
    ];

    protected $casts = [
        'birthdate' => 'date', // سترجع كـ Carbon تلقائيًا
        // ملاحظة: created_at و updated_at تُحوَّلان لـ Carbon تلقائيًا، ولا حاجة لتعريفهما هنا.
    ];

    public function getInstitutionStampUrlAttribute(): ?string
    {
        return $this->institution_stamp_path
            ? \Storage::disk('public')->url($this->institution_stamp_path)
            : null;
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
