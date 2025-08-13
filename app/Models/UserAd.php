<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAd extends Model
{
    protected $table = 'user_ads';
    protected $fillable = ['publish_id','ads_id','watches_role'];

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publish_id');
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ads_id');
    }
}
