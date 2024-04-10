<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;
    protected $table = 'articulo';

    protected $fillable = ['name','price', 'barcode','brand_id','status'];

    public function scopeFilter($query)
    {
        return $query->where('status', true)->orderBy('name')->pluck('name', 'id');
    }

    public function setting_product()
    {
        return $this->hasMany('App\Models\SettingProduct');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }

}
