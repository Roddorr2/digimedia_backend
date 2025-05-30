<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubtipoAdmin extends Model
{
    protected $fillable = [
        'description',
        'hierarchy'
    ];
}
