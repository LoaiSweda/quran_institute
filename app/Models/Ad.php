<?php
namespace App\Models;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $table = 'ads';

    protected $fillable = [
        'user_id','institute_id',
        'title','type_id','description','link','end_date','status','image',
    ];
    public function institute()
    {
        return $this->belongsTo(\App\Models\Institute::class);
    }


    public function type()
    {
        return $this->belongsTo(AdsType::class, 'type_id');
    }

    public function getComputedStatusAttribute()
    {
        if (Carbon::parse($this->end_date)->isPast()) {
            return 'expired';
        }
        return $this->status;
    }


    public function userAds()
    {
        return $this->hasMany(UserAd::class, 'ads_id');
    }


    public function publisher()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

}
