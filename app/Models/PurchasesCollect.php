<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PurchasesCollect extends Model
{
    protected $fillable = ['purchase_id',
                           'number',
                           'expiration',
                           'amount',
                           'residue'];

    protected $dates = ['expiration'];

    public function setExpirationAttribute($value)
    {
        $this->attributes['expiration'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase');
    }

    public function purchases_collect_payments()
    {
        return $this->hasMany('App\Models\PurchasesCollectPayment');
    }

    public function calendar_payments()
    {
        return $this->hasMany('App\Models\CalendarPayment');
    }
}