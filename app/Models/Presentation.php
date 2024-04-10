<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    protected $fillable = ['name', 'status', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeFilter($query)
    {
        return $query->where('status', true)->orderBy('name')->pluck('name', 'id');
    }
}
