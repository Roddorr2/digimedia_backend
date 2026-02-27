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
        'user_id',
        'parrafo',
        'imagen_url',
        'estado',
        'total_destinatarios',
        'envios_exitosos',
        'envios_fallidos',
        'envios_pendientes',
        'envios_hoy',
        'fecha_ultimo_envio',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_ultimo_envio' => 'date',
        'total_destinatarios' => 'integer',
        'envios_exitosos' => 'integer',
        'envios_fallidos' => 'integer',
        'envios_pendientes' => 'integer',
        'envios_hoy' => 'integer',
    ];

    /**
     * Relación con servicios
     */
    public function servicio()
    {
        return $this->belongsTo(servicios::class, 'id_servicio', 'id_servicio');
    }

    /**
     * Relación con usuario (quién creó la campaña)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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

    /**
     * Verifica si se alcanzó el límite diario (50 mensajes)
     */
    public function hasReachedDailyLimit(): bool
    {
        return $this->envios_hoy >= 50;
    }

    /**
     * Resetea el contador diario si cambió de día
     */
    public function resetDailyCounterIfNeeded(): void
    {
        $today = now()->format('Y-m-d');
        $lastSendDate = $this->fecha_ultimo_envio ? $this->fecha_ultimo_envio->format('Y-m-d') : null;

        if ($lastSendDate !== $today) {
            $this->envios_hoy = 0;
            $this->save();
        }
    }

    /**
     * Obtiene cuántos mensajes puede enviar hoy
     */
    public function getRemainingDailyQuota(): int
    {
        $this->resetDailyCounterIfNeeded();
        return max(0, 50 - $this->envios_hoy);
    }

    /**
     * Verifica si hay alguna campaña activa en proceso (FIFO)
     */
    public static function hasActiveCampaign(): bool
    {
        return self::whereIn('estado', ['en_proceso', 'pausada_hasta_mañana'])
            ->exists();
    }

    /**
     * Obtiene la campaña activa actualmente (si existe)
     */
    public static function getActiveCampaign(): ?self
    {
        return self::whereIn('estado', ['en_proceso', 'pausada_hasta_mañana'])
            ->orderBy('created_at', 'asc') // FIFO: primera que se creó
            ->first();
    }

    /**
     * Verifica si esta campaña puede ser iniciada (FIFO)
     */
    public function canBeStarted(): bool
    {
        // Solo campañas en borrador o pausadas pueden iniciarse
        if (!in_array($this->estado, ['borrador', 'pausada_hasta_mañana'])) {
            return false;
        }

        // Si hay otra campaña activa, no se puede iniciar
        $activeCampaign = self::getActiveCampaign();
        if ($activeCampaign && $activeCampaign->id_campania !== $this->id_campania) {
            return false;
        }

        return true;
    }

    /**
     * Verifica si la campaña está en borrador
     */
    public function isDraft(): bool
    {
        return $this->estado === 'borrador';
    }

    /**
     * Verifica si la campaña está pausada hasta mañana
     */
    public function isPausedUntilTomorrow(): bool
    {
        return $this->estado === 'pausada_hasta_mañana';
    }

    /**
     * Relación con mensajes de WhatsApp enviados (FASE 3)
     */
    public function mensajesWhatsApp()
    {
        return $this->hasMany(WatModal::class, 'campania_id', 'id_campania');
    }

    /**
     * Obtiene los mensajes fallidos que pueden reintentarse (FASE 3)
     */
    public function getFailedMessagesForRetry()
    {
        return WatModal::where('campania_id', $this->id_campania)
            ->where('estado', 0)
            ->where('puede_reintentar', true)
            ->where('intentos', '<', 3)
            ->with('modalServicio')
            ->get();
    }

    /**
     * Obtiene la cuenta de mensajes por intento (FASE 3)
     */
    public function getRetryStats(): array
    {
        $stats = WatModal::where('campania_id', $this->id_campania)
            ->selectRaw('intentos, COUNT(*) as total, SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) as fallidos')
            ->groupBy('intentos')
            ->get()
            ->keyBy('intentos')
            ->toArray();

        return [
            'intento_1' => $stats[1] ?? ['total' => 0, 'fallidos' => 0],
            'intento_2' => $stats[2] ?? ['total' => 0, 'fallidos' => 0],
            'intento_3' => $stats[3] ?? ['total' => 0, 'fallidos' => 0],
        ];
    }

    /**
     * Verifica si hay mensajes pendientes de reintento (FASE 3)
     */
    public function hasFailedMessagesToRetry(): bool
    {
        return WatModal::where('campania_id', $this->id_campania)
            ->where('estado', 0)
            ->where('puede_reintentar', true)
            ->where('intentos', '<', 3)
            ->exists();
    }
}
