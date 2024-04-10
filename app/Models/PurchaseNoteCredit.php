<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\DB;

class PurchaseNoteCredit extends Model 
{

    protected $fillable = [ 'purchase_id',
                            'purchase_invoice_id' ];

    public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase');
    }

    public function purchase_invoice()
    {
        return $this->belongsTo('App\Models\Purchase', 'purchase_invoice_id');
    }
}
