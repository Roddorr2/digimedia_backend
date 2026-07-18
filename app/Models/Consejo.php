<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consejo extends Model
{
    use HasFactory;
    protected $table = 'consejos';
    protected $primaryKey = 'id_consejo';
    public $timestamps = false;
    protected $fillable = ['texto', 'palabra', 'enlace', 'orden', 'id_blog_body'];

    public function blog_body(){
        return $this->belongsTo(BlogBody::class, 'id_blog_body', 'id_blog_body');
    }
}
