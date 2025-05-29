<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Blog extends Model
{
    use HasFactory;
    protected $table = 'blogs';
    protected $primaryKey = 'id_blog';
    public $timestamps = false;

    protected $fillable = [
        'id_blog_head',
        'id_blog_body',
        'id_blog_footer',
        'fecha',
        'link'
    ];

    public function head(){
        return $this->belongsTo(BlogHead::class, 'id_blog_head', 'id_blog_head');
    }

    public function body() {
    return $this->belongsTo(BlogBody::class, 'id_blog_body', 'id_blog_body');
    }

    public function footer(){
        return $this->belongsTo(BlogFooter::class, 'id_blog_footer', 'id_blog_footer');
    }

    public function card(){
        return $this->belongsTo(Card::class, 'id_blog', 'id_blog');
    }


}
