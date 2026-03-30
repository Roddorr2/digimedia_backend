<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Permiso;
use App\Models\Rol;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autorizado'
            ], 401);
        }

        // capacidades del token (roles)
        $userRoles = $request->user()->currentAccessToken()->abilities;

        // cargar roles y permisos requeridos en bloque para evitar N+1
        $roles = Rol::with('permisos:id_permiso')
            ->whereIn('nombre', $userRoles)
            ->get()
            ->keyBy('nombre');

        $permisos = Permiso::whereIn('slug', $permissions)
            ->get(['id_permiso', 'slug'])
            ->keyBy('slug');
        
        // verificar si el token tiene los permisos requeridos
        foreach ($userRoles as $roleName) {
            $rol = $roles->get($roleName);
            
            if ($rol) {
                $permissionIdsByRole = array_flip($rol->permisos->pluck('id_permiso')->all());

                foreach ($permissions as $permissionSlug) {
                    $permiso = $permisos->get($permissionSlug);
                    
                    if ($permiso && isset($permissionIdsByRole[$permiso->id_permiso])) {
                        return $next($request);
                    }
                }
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No tiene los permisos necesarios para acceder a este recurso'
        ], 403);
    }
}
