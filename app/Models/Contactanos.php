<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactanos extends Model
{
    use HasFactory;
    protected $table = 'contactanos';
    protected $primaryKey = 'id_contactanos';
    protected $fillable = [
        'nombre',
        'email',
        'numero',
        'mensaje',
        //agregando servicio
        'servicio',
        'fecha',
        'estado',
    ];
    public $timestamps = false;
}
