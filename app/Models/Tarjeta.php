<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarjeta extends Model
{
    use HasFactory;
    protected $table = 'tarjetas';
    protected $primaryKey = 'id_tarjeta';
    public $timestamps = false;
    protected $fillable = [
        'titulo',
        'descripcion',
        'id_blog_body',
        'enlace',
        'palabra',
        'titulo_color',
        'descripcion_color',
    ];

    public function blog_body(){
        return $this->belongsTo(BlogBody::class, 'id_blog_body', 'id_blog_body');
    }
}
