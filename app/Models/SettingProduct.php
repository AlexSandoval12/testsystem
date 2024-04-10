<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingProduct extends Model
{
    use HasFactory;
    protected $fillable = ['quantity','raw_materials_id', 'articulo_id','stage_id','production_qualities_id'];

    public function scopeFilter($query)
    {
        return $query->where('status', true)->orderBy('name')->pluck('name', 'id');
    }
    public function raw_material()
    {
        return $this->belongsTo('App\Models\RawMaterial','raw_materials_id');
    }
    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo','articulo_id');
    }

    public function stage()
    {
        return $this->belongsTo('App\Models\ProductionStage','stage_id');
    }

    public function qualities()
    {
        return $this->belongsTo('App\Models\ProductionQuality','production_qualities_id');
    }
}
