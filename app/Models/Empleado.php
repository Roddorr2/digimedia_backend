<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cloudinary\Cloudinary;
class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'dni',
        'telefono',
        'imagen_perfil',
        'imagen_perfil_url',
        'id_user',
        'id_rol',
        'id_subtipo_admin'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function subtipoAdmin()
    {
        return $this->belongsTo(SubtipoAdmin::class, 'id_subtipo_admin', 'id');
    }

    public function getImagenPerfilUrlAttribute()
    {
        if ($this->imagen_perfil) {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => [
                    'secure' => true
                ]
            ]);

            return $cloudinary->image($this->imagen_perfil)->toUrl();
        }
        return asset('images/default-profile.jpg');
    }

    public function hasSpecialAccess()
    {
        return cache()->remember("special-access-{$this->id_empleado}", 3600, function () {
            $allowedIds = config('special_access.employee_ids', []);
            return in_array($this->id_empleado, $allowedIds);
        });
    }

    public function blog()
    {
        return $this->hasMany(Blog::class, 'id_empleado', 'id_empleado');
    }

    public function getPrivilegeLevel()
    {
        $rolNombre = strtolower($this->rol->nombre);

        if ($rolNombre === "administrador" && $this->subtipoAdmin) {
            return $this->subtipoAdmin->hierarchy;
        }

        // Caso en que no se asigne un subtipo a administrador
        if ($rolNombre === "administrador") {
            return 80;
        }

        return match($rolNombre) {
            'ventas' => 30,
            'marketing' => 20,
            default => 0
        };
    }
}
