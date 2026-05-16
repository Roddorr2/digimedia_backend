<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionTiempo extends Model
{
    protected $table = 'configuracion_tiempo';
    protected $primaryKey = 'id_configuracion';

    protected $fillable = [
        'id_servicio',
        'tipo',
        'numero_mensaje',
        'unidad_tiempo',
        'valor_tiempo',
    ];

    protected $casts = [
        'valor_tiempo' => 'integer',
        'numero_mensaje' => 'integer',
    ];

    public function servicio()
    {
        return $this->belongsTo(servicios::class, 'id_servicio', 'id_servicio');
    }
}