<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ContactMedium extends Model
{

    protected $table = 'contact_mediums';
    protected $fillable = ['name', 'type', 'status', 'user_id'];

    
    public function contract()
    {
        return $this->hasMany('App\Models\contract');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }



}
