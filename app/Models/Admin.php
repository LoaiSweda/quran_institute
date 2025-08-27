<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $fillable = [
        'image','first_name','last_name','phone','address','birthdate','user_id'
    ];

    protected $casts = [
        'birthdate' => 'date', // سترجع كـ Carbon تلقائيًا
        // ملاحظة: created_at و updated_at تُحوَّلان لـ Carbon تلقائيًا، ولا حاجة لتعريفهما هنا.
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
