<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaniaWhatsApp extends Model
{
    use HasFactory;

    protected $table = 'campanias_whatsapp';
    protected $primaryKey = 'id_campania';

    protected $fillable = [
        'id_servicio',
        'parrafo',
        'imagen_url',
        'estado',
        'total_destinatarios',
        'envios_exitosos',
        'envios_fallidos',
        'envios_pendientes',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'total_destinatarios' => 'integer',
        'envios_exitosos' => 'integer',
        'envios_fallidos' => 'integer',
        'envios_pendientes' => 'integer',
    ];

    /**
     * Relación con servicios
     */
    public function servicio()
    {
        return $this->belongsTo(servicios::class, 'id_servicio', 'id_servicio');
    }

    /**
     * Verifica si la campaña está completada
     */
    public function isCompleted(): bool
    {
        return $this->estado === 'completada';
    }

    /**
     * Verifica si la campaña está en proceso
     */
    public function isInProgress(): bool
    {
        return $this->estado === 'en_proceso';
    }

    /**
     * Calcula el porcentaje de avance
     */
    public function getProgressPercentage(): float
    {
        if ($this->total_destinatarios === 0) {
            return 0;
        }

        $procesados = $this->envios_exitosos + $this->envios_fallidos;
        return round(($procesados / $this->total_destinatarios) * 100, 2);
    }

    /**
     * Actualiza el estado según el progreso
     */
    public function updateStatus(): void
    {
        $procesados = $this->envios_exitosos + $this->envios_fallidos;

        if ($procesados >= $this->total_destinatarios) {
            $this->estado = 'completada';
            $this->fecha_fin = now();
        }

        $this->save();
    }
}
