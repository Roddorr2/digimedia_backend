<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public static function invalidateEmpleado($id_empleado)
    {
        // Invalidar caché específico del empleado
        Cache::forget("empleado-{$id_empleado}");
        Cache::forget("special-access-{$id_empleado}");
        // Invalidar listados que podrían incluir este empleado
        Cache::tags(['empleados'])->flush();
    }
}
