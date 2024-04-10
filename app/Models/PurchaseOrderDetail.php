<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $fillable = ['purchases_order_id',
                           'material_id',
                           'quantity_received',
                           'description',
                           'presentation',
                           'price_cost',
                           'quantity',
                           'amount',
                           'residue'];

    public function purchase_order()
    {
        return $this->belongsTo('App\Models\PurchaseOrder','purchases_order_id');
    }

    public function raw_material()
    {
        return $this->belongsTo('App\Models\RawMaterial','material_id');
    }

    public function purchases_details()
    {
        return $this->hasMany('App\Models\PurchasesDetail');
    }

    public function purchases_movements_detail()
    {
        return $this->hasMany('App\Models\PurchasesMovementDetail', 'purchases_order_detail_id');
    }
    

}
