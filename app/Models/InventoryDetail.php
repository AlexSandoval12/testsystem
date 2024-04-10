<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class InventoryDetail extends Model
{
    protected $fillable = [ 'inventory_id',
                            'material_id',
                            'quantity',
                            'existence',
                            'old_cost' ];

    protected $dates = ['date'];

    public function purchases_product_inventory()
    {
        return $this->belongsTo('App\Models\Inventory', 'inventory_id');
    }

    public function material()
    {
        return $this->belongsTo('App\Models\RawMaterial', 'material_id');
    }
}
