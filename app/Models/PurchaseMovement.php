<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseMovement extends Model 
{
    use HasFactory;

    protected $fillable = [
                            'purchases_department_id',
    						'type_operation',
                            'type_movement',
                            'recived_person',
                            'movements_destiny_id',
                            'inventory_id',
                            'observation',
                            'invoice_number',
                            'invoice_date',
                            'date_payment',
                            'status',
                            'user_id',
                            'date_deleted',
                            'reason_deleted',
                            'user_deleted',
                            'accounting_seated',
                            'branch_id',
                            'deposit_id',
                            'currency_id', 
                            'invoice_condition', 
                            'invoice_stamped', 
                            'stamp_validity',
                            'purchase_id'
                          ];

    protected $dates = ['invoice_date', 'stamp_validity', 'date_payment', 'date_deleted'];

    public function setInvoiceDateAttribute($value)
    {
        if($value)
        {
            $this->attributes['invoice_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }else
        {
            $this->attributes['invoice_date'] = NULL;
        }        
    }

    public function setStampValidityAttribute($value)
    {
        if($value)
        {
            $this->attributes['stamp_validity'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }else
        {
            $this->attributes['stamp_validity'] = NULL;
        }        
    }

    public function setDatePaymentAttribute($value)
    {
        if($value)
        {
            $this->attributes['date_payment'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }else
        {
            $this->attributes['date_payment'] = NULL;
        }        
    }

    public function deposit()
    {
        return $this->belongsTo('App\Models\Deposit', 'deposit_id');
    } 

    public function deposit_destiny()
    {
        return $this->belongsTo('App\Models\Deposit', 'deposit_destiny_id');
    } 

    public function purchases_product_inventory()
    {
        return $this->belongsTo('App\Models\Inventory');
    } 

    public function movements_destiny()
    {
        return $this->belongsTo('App\Models\PurchasesMovement', 'movements_destiny_id');
    } 

    public function purchases_department()
    {
        return $this->belongsTo('App\Models\PurchasesRequestingDepartment', 'purchases_department_id');
    } 

    public function purchases_movement_details()
    {
        return $this->hasMany('App\Models\PurchaseMovementsDetail');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    } 

    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctor');
    } 

    public function user_deleted()
    {
        return $this->belongsTo('App\Models\User', 'user_id_deleted');
    }

    public function accounting_entry()
    {
        return $this->morphMany('App\Models\AccountingEntry', 'fromable');
    }

    public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get the branch that owns the PurchasesMovement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the currency that owns the PurchasesMovement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function currency()
    // {
    //     return $this->belongsTo(Currency::class);
    // }
}