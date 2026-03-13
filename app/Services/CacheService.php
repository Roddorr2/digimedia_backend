<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    const CACHE_DURATION = [
        'roles' => 86400,
        'empleados' => 14400,
        'plantillas' => 43200,
        'blogs' => 1800,
    ];

    public static function getRoles()
    {
        return Cache::remember(
            'roles-all',
            self::CACHE_DURATION['roles'],
            fn() => \App\Models\Rol::all()
        );
    }

    public static function getRolById($rolId)
    {
        return Cache::remember(
            "rol-{$rolId}",
            self::CACHE_DURATION['roles'],
            fn() => \App\Models\Rol::find($rolId)
        );
    }

    public static function invalidateRoles()
    {
        Cache::forget('roles-all');
        for ($i = 1; $i <= 20; $i++) {
            Cache::forget("rol-{$i}");
        }
    }

    public static function getEmpleadoConRelaciones($empleadoId)
    {
        return Cache::remember(
            "empleado-completo-{$empleadoId}",
            self::CACHE_DURATION['empleados'],
            fn() => \App\Models\Empleado::with(['user', 'rol', 'subtipoAdmin'])
                ->find($empleadoId)
        );
    }

    public static function invalidateEmpleado($empleadoId)
    {
        Cache::forget("empleado-completo-{$empleadoId}");
    }

    public static function buscarBlogs($termino = '', $page = 1, $duration = 1800)
    {
        $cacheKey = "search-blogs-{$termino}-{$page}";

        return Cache::remember(
            $cacheKey,
            $duration,
            fn() => \App\Models\Blog::buscar($termino)
                ->conRelaciones()
                ->paginate(15)
        );
    }

    public static function getBlogsPublicados($duration = 1800)
    {
        return Cache::remember(
            'blogs-publicados',
            $duration,
            fn() => \App\Models\Blog::conRelaciones()
                ->reciente()
                ->limit(20)
                ->get()
        );
    }

    public static function invalidateBlogs()
    {
        Cache::tags(['blogs'])->flush();
    }

    public static function isAvailable()
    {
        return config('cache.default') !== null;
    }

    public static function getInfo()
    {
        return [
            'driver' => config('cache.default'),
            'available' => self::isAvailable(),
        ];
    }

    public static function clearSensitiveData()
    {
        self::invalidateRoles();
        
        for ($i = 1; $i <= 1000; $i++) {
            self::invalidateEmpleado($i);
        }
        
        self::invalidateBlogs();
    }
}
