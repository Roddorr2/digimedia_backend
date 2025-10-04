<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class modalservicios extends Model
{
    use HasFactory;
    protected $table = 'modalservicios';
    protected $primaryKey = 'id_modalservicio';

    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'id_servicio', // FK
        'fecha',
        'estado'
    ];

    public $timestamps = false;

    public function servicio()
    {
        return $this->belongsTo(Servicios::class, 'id_servicio', 'id_servicio');
    }

    public function watModal()
    {
        return $this->hasMany(WatModal::class, 'id_modalservicio', 'id_modalservicio');
    }
    public function emailModal()
    {
        return $this->hasMany(EmailModal::class, 'id_modalservicio', 'id_modalservicio');
    }

}
