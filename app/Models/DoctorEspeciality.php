<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorEspeciality extends Model
{
    protected $fillable = ['doctor_id', 'especiality_id'];

    public function doctor()
    {
    	return $this->belongsTo('App\Models\Doctor');
    }

    public function especiality()
    {
    	return $this->belongsTo('App\Models\Especiality');
    }
}
