<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Subservicio extends Model
{
    protected $primaryKey = 'id_subservicio';
    public $timestamps = false;
    protected $fillable = ['id_servicio', 'nombre', 'slug'];

    public function servicio()
    {
        return $this->belongsTo(servicios::class, 'id_servicio');
    }

    public function popupConfig()
    {
        return $this->morphOne(PopupConfig::class, 'popupable');
    }

    public function scopeByServicio(Builder $query, int $idServicio): Builder
    {
        return $query->where('id_servicio', $idServicio);
    }
}