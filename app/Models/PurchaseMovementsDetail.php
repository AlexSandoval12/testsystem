<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseMovementsDetail extends Model
{
    protected $fillable = [	'purchases_movement_id',
                            'purchases_existence_id',
                            'raw_materials_id',                            
    						'purchases_order_detail_id',
                            'price_cost',
    						'quantity',
                            'affects_stock'];
    
    public function purchases_movement()
    {
        return $this->belongsTo('App\Models\PurchaseMovement', 'purchase_movement_id');
    }

    public function raw_material()
    {
        return $this->belongsTo('App\Models\RawMaterial','raw_materials_id');
    }

    public function purchases_order_detail()
    {
        return $this->belongsTo('App\Models\PurchaseOrderDetail');
    }

    public function purchases_existence()
    {
        return $this->belongsTo('App\Models\PurchasesExistence');
    }
    public function restocking_detail()
    {
        return $this->belongsTo('App\Models\RestockingDetail');
    }
    
}