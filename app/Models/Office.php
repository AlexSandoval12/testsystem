<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;
    protected $fillable = ['name','phoneNumber','city_id','address', 'status'];

    public function doctors()
    {
        return $this->belongsToMany('App\Models\Doctor', 'doctor_offices');
    }

}
