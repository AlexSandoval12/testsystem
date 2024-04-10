<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $fillable = [
                            'date',
                            'number',
                            'branch_id',
                            'condition',
                            'provider_id',
                            'razon_social',
                            'ruc',
                            'phone',
                            'address',
                            'observation',
                            'amount',
                            'status',
                            'user_id'
                        ];
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
    public function purchase_order_details()
    {
        return $this->hasMany('App\Models\PurchaseOrderDetail','purchases_order_id');
    }
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }
    public function Branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
