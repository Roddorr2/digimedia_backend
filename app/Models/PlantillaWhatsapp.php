<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PlantillaWhatsapp extends Model
{
    protected $table = 'plantillas_whatsapp';
    protected $primaryKey = 'id_plantilla_whatsapp';

    protected $fillable = [
        'plantillable_id',
        'plantillable_type',
        'numero_plantilla',
        'nombre',
        'mensaje',
        'imagen_url',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'plantillable_id'  => 'integer',
        'numero_plantilla' => 'integer',
        'created_by'       => 'integer',
        'updated_by'       => 'integer',
    ];

    public function plantillable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function getIdServicioAttribute(): ?int
    {
        if ($this->plantillable_type === servicios::class) {
            return $this->plantillable_id;
        }

        if ($this->plantillable_type === Subservicio::class && $this->relationLoaded('plantillable') && $this->plantillable) {
            return $this->plantillable->id_servicio;
        }

        return null;
    }

    public function getPlantillableTypeNameAttribute(): string
    {
        return match ($this->plantillable_type) {
            servicios::class  => 'servicio',
            Subservicio::class => 'subservicio',
            default            => 'desconocido',
        };
    }

    public function scopeByServicio($query, int $idServicio)
    {
        return $query->where('plantillable_type', servicios::class)
                     ->where('plantillable_id', $idServicio);
    }

    public function scopeBySubservicio($query, int $idSubservicio)
    {
        return $query->where('plantillable_type', Subservicio::class)
                     ->where('plantillable_id', $idSubservicio);
    }

    public function scopeByNumero($query, int $numero)
    {
        return $query->where('numero_plantilla', $numero);
    }

    public function getMensajeProcessed(array $params = []): string
    {
        $mensaje = $this->mensaje;

        foreach ($params as $key => $value) {
            $mensaje = str_replace("{{$key}}", $value, $mensaje);
        }

        return $mensaje;
    }

    public function getImagenFullUrl(): string
    {
        if (str_starts_with($this->imagen_url, 'http')) {
            return $this->imagen_url;
        }

        return url($this->imagen_url);
    }
}
