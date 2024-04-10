<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especiality extends Model
{
    protected $fillable = ['name', 'duration', 'status'];

    public function doctors()
    {
    	return $this->belongsToMany('App\Models\Doctor', 'doctor_especialities');
    }
}
