<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesExistence extends Model
{
    protected $fillable = [	'raw_material_id',
                            'type',
                            'deposit_id',
                            'quantity',
                            'residue',
                            'price_cost',
                            'price_cost_iva'];
                            
    public function raw_material()
    {
        return $this->belongsTo('App\Models\RawMaterial');
    }

    public function deposit()
    {
        return $this->belongsTo('App\Models\Deposit');
    }

    public function social_reason()
    {
        return $this->belongsTo('App\Models\SocialReason');
    }
}