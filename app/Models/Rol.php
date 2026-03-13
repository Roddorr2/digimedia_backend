<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\CacheService;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_rol', 'id_rol');
    }
    
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'role_permission', 'id_rol', 'id_permiso');
    }

    protected static function booted()
    {
        static::updated(function ($rol) {
            CacheService::invalidateRoles();
        });

        static::created(function ($rol) {
            CacheService::invalidateRoles();
        });

        static::deleted(function ($rol) {
            CacheService::invalidateRoles();
        });
    }
}
