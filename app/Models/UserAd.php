<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAd extends Model
{
    protected $table = 'user_ads';
    protected $fillable = ['publish_id','ads_id','watches_role','is_read','read_at'];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];
    public function publisher()
    {
        return $this->belongsTo(User::class, 'publish_id');
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ads_id');
    }

    public function scopeUnreadForRole($q, string $roleLower)
    {
        return $q->whereRaw('LOWER(watches_role) = ?', [$roleLower])
                 ->where('is_read', false);
    }
}
