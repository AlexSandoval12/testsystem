<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = ['first_name', 'last_name', 'address', 'ruc','neighborhood','razon_social','civil_status','document_number','gender','observation','status','nationality_id'];


    public function scopeFilter($query)
    {
        return $query->where('status', true)->orderBy('id')->pluck('id', 'id');
    }
    
    public function getFullnameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
