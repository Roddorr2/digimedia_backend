<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WatModal extends Model
{
    use HasFactory;

    protected $table = 'modal_wats';
    protected $primaryKey = 'id_modal_wat';
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'error',
        'id_modalservicio',
        'number_message',
        'fecha',
        'intentos',
        'campania_id',
        'puede_reintentar',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'puede_reintentar' => 'boolean',
        'intentos' => 'integer',
    ];

    /**
     * Relación con modalservicios
     */
    public function modalServicio(){
        return $this->belongsTo(modalservicios::class,'id_modalservicio', 'id_modalservicio');
    }

    /**
     * Relación con campaña de WhatsApp
     */
    public function campania()
    {
        return $this->belongsTo(CampaniaWhatsApp::class, 'campania_id', 'id_campania');
    }

    /**
     * Verifica si el envío falló
     */
    public function hasFailed(): bool
    {
        return $this->estado === 0 || $this->estado === false;
    }

    /**
     * Verifica si puede reintentarse
     */
    public function canRetry(): bool
    {
        return $this->puede_reintentar && $this->intentos < 3 && $this->hasFailed();
    }

    /**
     * Incrementa el número de intentos
     */
    public function incrementRetry(): void
    {
        $this->intentos++;
        
        // Deshabilitar reintentos si alcanzó el máximo
        if ($this->intentos >= 3) {
            $this->puede_reintentar = false;
        }
        
        $this->save();
    }
}
