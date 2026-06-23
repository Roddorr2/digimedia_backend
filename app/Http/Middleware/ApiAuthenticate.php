<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar autenticación Sanctum
        if (!Auth::check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated - Token missing or invalid'
            ], 401);
        }

        return $next($request);
    }
}
