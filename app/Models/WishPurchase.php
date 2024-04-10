<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishPurchase extends Model
{
    use HasFactory;
    protected $fillable = [
                            'date',
                            'branch_id',
                            'number',
                            'provider_id',
                            'status',
                            'user_id',
                            'observation'
                        ];
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
    public function wish_purchase_details()
    {
        return $this->hasMany('App\Models\WishPurchaseDetail');
    }
    public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function purchase_budgets()
    {
        return $this->hasMany('App\Models\PurchaseBudget');
    }
}
