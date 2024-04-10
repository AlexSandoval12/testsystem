<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    protected $fillable = [ 'name',
                            'ruc',
                            'address',
                            'phone',
                            'user_id',
                            'status'];

    public function scopeFilter($query)
    {
        return $query->where('status', true)->orderBy('name')->pluck('name', 'id');
    }
}
