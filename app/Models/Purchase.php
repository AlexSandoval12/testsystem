<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{

    protected $fillable = ['id',
                           'date',
                           'branch_id',
                           'condition',
                           'stamped',
                           'type',
                           'number',
                           'provider_id',
                           'razon_social',
                           'ruc',
                           'phone',
                           'address',
                           'observation',
                           'amount',
                           'total_excenta',
                           'total_iva5',
                           'total_iva10',
                           'amount_iva5',
                           'amount_iva10',
                           'stamped_validity',
                           'status',
                           'user_id',
                           'date_deleted',
                           'reason_deleted',
                           'user_delete'
                        ];

    protected $appends = ['fullnumber'];

    protected $dates = ['date', 'stamped_validity', 'date_deleted', 'first_expiration'];

    public function getFullnumberAttribute()
    {
        return config('constants.type_purchases.'. $this->attributes['type']).' '.$this->attributes['number'];
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function setStampedValidityAttribute($value)
    {
        if($value)
        {
            $this->attributes['stamped_validity'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }else
        {
            $this->attributes['stamped_validity'] = NULL;
        }
    }

    public function setFirstExpirationAttribute($value)
    {
        $this->attributes['first_expiration'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function setNumberAttribute($value)
    {
        $this->attributes['number'] = str_replace('_', '', $value);
    }

    public function cash_box()
    {
        return $this->belongsTo('App\Models\CashBox');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }    

    public function user()
    {
        return $this->belongsto('App\Models\User');
    }

    public function user_delete()
    {
        return $this->belongsto('App\Models\User', 'user_deleted');
    }

    public function purchase_details()
    {
        return $this->hasMany('App\Models\PurchaseDetail');
    }

    public function note_credits()
    {
        return $this->hasMany('App\Models\PurchaseNoteCredit');
    }

    public function purchases_accounting_plans()
    {
        return $this->hasMany('App\Models\PurchasesAccountingPlan');
    }

    public function purchases_advance()
    {
        return $this->hasMany('App\Models\PurchasesAdvance');
    }

    public function purchases_cost_centers()
    {
        return $this->hasMany('App\Models\PurchasesCostCenter');
    }

    public function accounting_entry()
    {
        return $this->morphMany('App\Models\AccountingEntry', 'fromable');
    }

    public function purchases_payments()
    {
        return $this->hasMany('App\Models\PurchasesPayment');
    }

    public function purchases_collects()
    {
        return $this->hasMany('App\Models\PurchasesCollect');
    }

    public function purchases_collect_payments()
    {
        return $this->hasMany('App\Models\PurchasesCollectPayment');
    }

    public function scopeActive($query)
    {
        return $query->where('purchases.status', true);
    }

    public function payment_services_authorizations()
    {
      return $this->hasMany('App\Models\PaymentServicesAuthorization');
    }

    public function services_authorization()
    {
        return $this->belongsTo('App\Models\PaymentServicesAuthorization', 'payment_services_authorization_id');
    }

    public function cancel_user()
    {
        return $this->belongsTo('App\Models\User', 'cancel_user_id');
    }

    public function pending_receipts()
    {
        return $this->hasMany('App\Models\PurchasesPendingReceipt');
    }

    public function purchases_pendings()
    {
        return $this->hasMany('App\Models\PurchasesPendingReceipt');
    }

    public function purchase_op_massive()
    {
        return $this->hasOne('App\Models\OpMassiveDetail','purchase_op_id');
    }

    public function calendar_payments()
    {
        return $this->hasMany('App\Models\CalendarPayment');
    }

}

