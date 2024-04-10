<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SellerTeam extends Model
{
    protected $fillable = ['name', 'enterprise_id', 'goal', 'from_time', 'until_time', 'type_team', 'user_id', 'status'];

    public function enterprise()
    {
    	return $this->belongsTo('App\Models\Enterprise');
    }

    public function seller_team_users()
    {
        return $this->hasMany('App\Models\SellerTeamUser');
    }

    public function scopeGetAllCached()
    {
        return Cache::rememberForever('models.all.sellerteam', function(){
            return self::where('status', true)->get();
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($model){
            Cache::forget('model.all.sellerteam');
        });

        static::updated(function($model){
            Cache::forget('model.all.sellerteam');
        });

        static::deleted(function($model){
            Cache::forget('model.all.sellerteam');
        });
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function details()
    {
        return $this->hasMany('App\Models\SellerTeamDetail');
    }
}
