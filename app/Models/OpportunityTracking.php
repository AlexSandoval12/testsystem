<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class OpportunityTracking extends Model 
{

    protected $fillable = ['opportunity_id', 'attended', 'contact_form', 'not_attended', 'observation', 'status', 'user_id', 'call_again', 'closer', 'sold', 'reject', 'created_at', 'updated_at', 'reassigned', 'action', 'scheduled'];

    protected $dates = ['call_again'];

    public function setCallAgainAttribute($value)
    {
        $this->attributes['call_again'] = $value ? Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d') : NULL;
    }

    public function opportunity()
    {
        return $this->belongsTo('App\Models\Opportunity', 'opportunity_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function call_center_call_phone()
    {
        return $this->HasOne('App\Models\CallCenterCallPhone');
    }
}
