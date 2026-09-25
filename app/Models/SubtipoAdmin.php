<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubtipoAdmin extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'description',
        'hierarchy'
    ];
}
