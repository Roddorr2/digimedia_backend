<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantillaWhatsapp extends Model
{
    protected $table = 'plantillas_whatsapp';
    protected $primaryKey = 'id_plantilla_whatsapp';

    protected $fillable = [
        'id_servicio',
        'numero_plantilla',
        'nombre',
        'mensaje',
        'imagen_url',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'numero_plantilla' => 'integer',
        'id_servicio' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Relación con el servicio
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(servicios::class, 'id_servicio', 'id_servicio');
    }

    /**
     * Usuario que creó la plantilla
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Usuario que actualizó la plantilla
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Scope para obtener plantillas por servicio
     */
    public function scopeByServicio($query, int $idServicio)
    {
        return $query->where('id_servicio', $idServicio);
    }

    /**
     * Scope para obtener una plantilla específica
     */
    public function scopeByNumero($query, int $numero)
    {
        return $query->where('numero_plantilla', $numero);
    }

    /**
     * Reemplaza los placeholders en el mensaje
     */
    public function getMensajeProcessed(array $params = []): string
    {
        $mensaje = $this->mensaje;
        
        foreach ($params as $key => $value) {
            $mensaje = str_replace("{{$key}}", $value, $mensaje);
        }
        
        return $mensaje;
    }

    /**
     * Obtiene la URL completa de la imagen
     */
    public function getImagenFullUrl(): string
    {
        // Si ya es URL completa (http/https), retornarla directamente
        if (str_starts_with($this->imagen_url, 'http')) {
            return $this->imagen_url;
        }
        
        // Si es ruta local, construir URL completa
        return url($this->imagen_url);
    }
}
