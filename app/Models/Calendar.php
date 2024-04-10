<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'calendar';
    protected $fillable = [
                'client_id',
                'start',
                'end',
                'doctor_id',
                'office_id',
                'first_consultation',
                'observation',
                'observation_doctor',
                'user_id',
                'status'
    ];

    protected $dates = ['start','end'];

    public function setStartAttribute($value)
    {
        $this->attributes['start'] = Carbon::createFromFormat('d/m/Y H:i', $value)->format('Y-m-d H:i:00');
    }

    public function setEndAttribute($value)
    {
        $this->attributes['end'] = Carbon::createFromFormat('d/m/Y H:i', $value)->format('Y-m-d H:i:00');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctor');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }
 
}
