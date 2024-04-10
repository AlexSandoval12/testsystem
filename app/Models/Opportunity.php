<?php

namespace App\Models;

use App\Models\SalesOpportunityTracking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model 
{
    protected $fillable = [
                            // 'enterprise_id',
                            'contact_medium_id',
                            'fullname',
                            'dental_office',
                            'phone',
                            'email',
                            'document_number',
                            'observation',
                            'status',
                            'seller_id',
                            'user_id',
                            'deleted_at',
                            'deleted_reason',
                            'deleted_user_id',
                            // 'closer_id',
                            // 'creator',
                            // 'insurance_id',
                            'type_plan',
                            'lead',
                            // 'processed_at',
                            // 'closed_at',
                            'selled_at',
                            'rejected_at',
                            'rejected_motive_id',
                            // 'seller_city',
                            // 'closed_in',
                            'city_id',
                            'address',
                            // 'deadline',
                            'branch_id',
                            // 'ad_id',
                            // 'form_id',
                            'json_request',
                            // 'contract_promotion_id',
                            'contract_type',
                            // 'latitude',
                            // 'longitude',
                            // 'online_call',
                            // 'contract_id',
                            'amount',
                            // 'drawer_sale_at',
                            // 'ip_address','notificated',
                            'ruc',
                            'client_type',
                            // 'number_without_prefix',
                            'prefix',
                            'contact_name',
                            'contact_charge',
                            // 'section',
                            // 'dental_budget_id',
                            // 'location',
                            // 'scheduled',
                            // 'message_id'
                        ];

    protected $dates = ['deleted_at', 'deadline', 'processed_at', 'closed_at', 'selled_at', 'rejected_at', 'drawer_sale_at'];

    protected $casts = ['json_request' => 'array',];

    protected $appends = ['first_call_again', 'coordinates', 'last_day_tracking']; 

    public function setDocumentNumberAttribute($value)
    {
        $this->attributes['document_number'] = str_replace('.', '', $value);
    }

    public function setDeadlineAttribute($value)
    {
        $this->attributes['deadline'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

     public function getFirstCallAgainAttribute()
     {
         $trackings = OpportunityTracking::where('opportunity_id', $this->attributes['id'])->where('status', 1)->where('call_again', '>=', date('Y-m-d'))->get();
         return $trackings->max('call_again');
     }

    // public function getLastDayTrackingAttribute()
    // {
    //     $trackings = SalesOpportunityTracking::where('sales_opportunity_id', $this->attributes['id'])->where('status', 1)->orderBy('created_at', 'desc')->first();
    //     return $trackings ? $trackings->created_at : null;
    // }

    public function getCoordinatesAttribute()
    {
        if ($this->attributes['latitude'] && $this->attributes['longitude'])
        {
            return $this->attributes['latitude'] . ',' .  $this->attributes['longitude'];
        }
        else
        {
            return '';
        }
    }

    public function files()
    {
        return $this->hasMany('App\Models\SalesOpportunityFile');
    }

    public function trackings()
    {
        return $this->hasMany('App\Models\OpportunityTracking', 'opportunity_id');
    }

    public function insurance()
    {
        return $this->belongsTo('App\Models\Insurance');
    }

    public function enterprise()
    {
        return $this->belongsTo('App\Models\Enterprise');
    }

    public function rejected_motive()
    {
        return $this->belongsTo('App\Models\SalesOpportunityRejectionMotive', 'rejected_motive_id');
    }

    public function contact_medium()
    {
        return $this->belongsTo('App\Models\ContactMedium');
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\User', 'seller_id');
    }

    public function closer()
    {
        return $this->belongsTo('App\Models\User', 'closer_id');
    }

    public function deleted_user()
    {
        return $this->belongsTo('App\Models\User', 'deleted_user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function contract_promotion()
    {
        return $this->belongsTo('App\Models\ContractPromotion');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }

    public function sales_movements()
    {
        return $this->hasMany('App\Models\SalesOpportunityMovement');
    }

    public function contract()
    {
        return $this->belongsTo('App\Models\Contract');
    }

    public function crm_products()
    {
        return $this->hasMany('App\Models\CrmProduct', 'opportunity_id');
    }

    public function crm_contacts()
    {
        return $this->hasMany('App\Models\CrmContact', 'opportunity_id');
    }

    public function dental_budget()
    {
        return $this->belongsTo('App\Models\DentalBudget');
    }
}
