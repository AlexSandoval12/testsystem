<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class DoctorSchedule extends Model 
{
    protected $fillable = [ 'doctor_id',
                            'office_id',
                            'days',
                            'work_start',
                            'work_end',
                            'break_start',
                            'break_end',
                            'status',
                            'break_interval'
                          ];

    public function getWorkStartAttribute($date)
    {
        return Carbon::parse($date);
    }

    public function getWorkEndAttribute($date)
    {
        return Carbon::parse($date);
    }

    public function getBreakStartAttribute($date)
    {   
        if($this->attributes['break_start']) 
        {
            return Carbon::parse($date);
        }

        return null;
    }

    public function getBreakEndAttribute($date)
    {
        if($this->attributes['break_start']) 
        {
            return Carbon::parse($date);
        }
        
        return null;
    }

    public function doctor()
    {
    	return $this->belongsTo('App\Models\Doctor');
    }

    public function office()
    {
    	return $this->belongsTo('App\Models\Office');
    }

    public function details()
    {
        return $this->hasMany('App\Models\DoctorScheduleDetail');
    }

    public function people()
    {
        return $this->belongsTo('App\Models\Person', 'people_id');
    }
}
