<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupConfig extends Model
{
    protected $table = 'popup_configs';
    protected $primaryKey = 'id_popup_config';
    public $timestamps = true;

    protected $fillable = [
        'id_subservicio',
        'title_text',
        'button_text',
        'service_color',
        'trigger_time',
        'left_image_url',
        'left_opacity',
        'right_image_url',
        'right_opacity',
        'mobile_image_url',
        'mobile_opacity',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'left_opacity' => 'integer',
        'right_opacity' => 'integer',
        'mobile_opacity' => 'integer',
        'trigger_time' => 'integer'
    ];

    public function subservicio(): BelongsTo
    {
        return $this->belongsTo(Subservicio::class, 'id_subservicio');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeBySubservicio($query, $id)
    {
        return $query->where('id_subservicio', $id);
    }

    public function scopeByServicio($query, $idServicio)
    {
        return $query->whereHas('subservicio', function ($q) use ($idServicio) {
            $q->where('id_servicio', $idServicio);
        });
    }
}
