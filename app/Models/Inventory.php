<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Inventory extends Model
{
    protected $fillable = [ 'date',
                            'deposit_id',
                            'user_id',
                            'status',
                            'observation',
                            'user_deleted_id',
                            'reason_deleted',
                            'date_deleted' ];

    protected $dates = ['date'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function deposit()
    {
        return $this->belongsTo('App\Models\Deposit');
    }

    public function purchases_product_inventory_details()
    {
        return $this->hasMany('App\Models\InventoryDetail', 'inventory_id');
    }

    public function purchases_movements()
    {
        return $this->hasMany('App\Models\PurchasesMovement');
    }

    public function scopeFilter($query)
    {
        return $query->where('status', true);
    }
}
