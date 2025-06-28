<?php
namespace App\Models;
use Illuminate\Support\Carbon; 
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $table = 'ads';

    protected $fillable = [
        'title','description','link','end_date','user_id','type_id','status','image'
    ];

    // علاقة بنوع الإعلان
    public function type()
    {
        return $this->belongsTo(AdsType::class, 'type_id');
    }

     // صفة محسوبة للحالة الحقيقية
    public function getComputedStatusAttribute()
    {
        // إذا انتهى التاريخ
        if (Carbon::parse($this->end_date)->isPast()) {
            return 'expired';
        }
        return $this->status; // "active" أو "inactive" أو غيرها
    }


    public function userAds()
    {
        return $this->hasMany(UserAd::class, 'ads_id');
    }

    public function publisher()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'user_ads',
            'ads_id',
            'publish_id'
        )->distinct();
    }

}
