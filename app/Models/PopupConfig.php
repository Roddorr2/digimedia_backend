<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PopupConfig extends Model
{
    protected $table = 'popup_configs';
    protected $primaryKey = 'id_popup_config';
    public $timestamps = true;

    protected $fillable = [
        'popupable_type',
        'popupable_id',
        'button_text',
        'button_color',       
        'service_color',
        'service_color_2',    
        'gradient_direction', 
        'trigger_time',
        'trigger_type',
        'layout',
        'show_logo',
        'left_text',
        'left_image_url',
        'left_opacity',
        'left_alt',           
        'right_image_url',
        'right_opacity',
        'right_alt',          
        'mobile_image_url',
        'mobile_opacity',
        'mobile_alt',         
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'left_opacity' => 'integer',
        'right_opacity' => 'integer',
        'mobile_opacity' => 'integer',
        'trigger_time' => 'integer',
        'trigger_type' => 'string',
        'layout' => 'string',
        'show_logo' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    public function popupable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getIdServicioAttribute(): ?int
    {
        if (!$this->popupable) {
            return null;
        }

        if ($this->popupable_type === servicios::class) {
            return $this->popupable->id_servicio;
        }

        if ($this->popupable_type === Subservicio::class) {
            return $this->popupable->id_servicio;
        }

        return null;
    }

    public function getPopupableTypeNameAttribute(): string
    {
        return match ($this->popupable_type) {
            servicios::class => 'servicio',
            Subservicio::class => 'subservicio',
            default => 'desconocido',
        };
    }

    // Relaciones existentes (sin cambios)
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes actualizados para polimorfismo
    public function scopeBySubservicio(Builder $query, int $id): Builder
    {
        return $query->where('popupable_type', Subservicio::class)
                     ->where('popupable_id', $id);
    }

    public function scopeByServicio(Builder $query, int $idServicio): Builder
    {
        return $query->where('popupable_type', servicios::class)
                     ->where('popupable_id', $idServicio);
    }
}