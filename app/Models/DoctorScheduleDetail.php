<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorScheduleDetail extends Model
{
    protected $fillable = ['doctor_schedule_id', 'speciality_id', 'from_time', 'until_time'];

    public function doctor_schedule()
    {
        return $this->belongsTo('App\Models\DoctorSchedule');
    }

    public function speciality()
    {
        return $this->belongsTo('App\Models\Especiality', 'speciality_id');
    }
}
