<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $fillable = ['purchase_id',
                           'material_id',
                           'description',
                           'quantity',
                           'amount',
                           'excenta',
                           'iva5',
                           'iva10'];

    protected $appends = ['subtotal'];

    public function setDiscountAmountAttribute($value)
    {
        $this->attributes['discount_amount'] = str_replace('.', '', $value);
    }

    public function getSubtotalAttribute()
    {
        return $this->attributes['quantity'] * $this->attributes['amount'];
    }

    public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase');
    }

    public function material()
    {
        return $this->belongsTo('App\Models\RawMaterial');
    }

    public function purchases_order_detail()
    {
        return $this->belongsTo('App\Models\PurchaseOrderDetail');
    }

    public function accounting_plan()
    {
        return $this->belongsTo('App\Models\AccountingPlan');
    }

    public function purchase_order_detail()
    {
        return $this->belongsTo('App\Models\PurchasesMovementDetail', 'purchases_order_detail_id');
    }
}
