<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;
    protected $table = 'cards';
    protected $primaryKey = 'id_card';
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'public_image',
        'url_image',
        'id_plantilla',
        'id_blog',
        'id_empleado',
        'estado_publicacion'
    ];

    public function blog() {
        return $this->belongsTo(Blog::class, 'id_blog', 'id_blog');
    }

    public function empleado() {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }

    // ============================================================
    // SCOPES para queries reutilizables
    // ============================================================

    /**
     * Filtrar solo cards publicadas
     * Uso: Card::publicadas()->get()
     */
    public function scopePublicadas($query)
    {
        return $query->where('estado_publicacion', true);
    }

    /**
     * Filtrar solo cards en borrador
     * Uso: Card::borrador()->get()
     */
    public function scopeBorrador($query)
    {
        return $query->where('estado_publicacion', false);
    }

    /**
     * Filtrar cards de un empleado específico
     * Uso: Card::delEmpleado(1)->get()
     */
    public function scopeDelEmpleado($query, $empleadoId)
    {
        return $query->where('id_empleado', $empleadoId);
    }

    /**
     * Filtrar cards de un blog específico
     * Uso: Card::delBlog(1)->get()
     */
    public function scopeDelBlog($query, $blogId)
    {
        return $query->where('id_blog', $blogId);
    }

    /**
     * Ordenar por ID ascendente (por defecto de API)
     * Uso: Card::ordenado()->get()
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('id_card', 'asc');
    }

    /**
     * Cargar relaciones comúnmente usadas
     * Uso: Card::conRelacionesCompletas()->get()
     */
    public function scopeConRelacionesCompletas($query)
    {
        return $query->with(['blog.head', 'blog.body', 'blog.footer', 'empleado']);
    }

    /**
     * Cargar solo relaciones básicas
     * Uso: Card::conRelaciones()->get()
     */
    public function scopeConRelaciones($query)
    {
        return $query->with(['blog', 'empleado']);
    }

    /**
     * Cargar solo relación blog con sus detalles
     * Uso: Card::conDetallesBlog()->get()
     */
    public function scopeConDetallesBlog($query)
    {
        return $query->with(['blog.head', 'blog.body', 'blog.footer']);
    }

}
