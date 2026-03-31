<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$requirements): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autorizado'
            ], 401);
        }

        // roles embebidos en el token de acceso actual
        $tokenAbilities = $user->currentAccessToken()?->abilities ?? [];

        foreach ($requirements as $requirement) {
            if (in_array($requirement, $tokenAbilities, true)) {
                return $next($request);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No tiene los permisos necesarios para acceder a este recurso'
        ], 403);
    }
}
