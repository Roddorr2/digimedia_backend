<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\HasMany;

class servicios extends Model
{
    protected $table = 'servicios';

    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'id_servicio',
        'nombre',
        'descripcion'
    ];

    public $timestamps = false;

    public function modalservicios(): HasMany
    {
        return $this->hasMany(modalservicios::class, 'id_servicio', 'id_servicio');
    }

    public function reclamacion()
    {
        return $this->belongsTo(libroreclamacion::class, 'id_servicio');
    }

    public function popupConfig()
    {
        return $this->morphOne(PopupConfig::class, 'popupable');
    }
}