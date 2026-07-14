<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonio extends Model
{
    protected $primaryKey = 'id_testimonio';

    protected $fillable = ['nombre', 'cargo', 'texto', 'rating','activo','fecha_testimonio', 'imagen_public_id', 'imagen_url'];
}