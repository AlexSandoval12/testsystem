<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = ['first_name','last_name','document_number','office_id','corresponding_user_id', 'status'];

    public function offices()
    {
        return $this->belongsToMany('App\Models\Office', 'doctor_offices')->withPivot('doctor_id', 'office_id');
    }

    public function getFullnameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    public function especialities()
    {
        return $this->belongsToMany('App\Models\Especiality', 'doctor_especialities')->withTimestamps();
    }

    public function doctor_especialities()
    {
        return $this->hasMany('App\Models\DoctorEspeciality');
    }
}
