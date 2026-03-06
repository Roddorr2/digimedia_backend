<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Valida que la petición incluya un API Key válido
     * Usado por whatsapp-service (Node.js) para consultar plantillas
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        
        // API Key configurada en .env y config/services.php
        $validApiKey = config('services.whatsapp.api_key');

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key inválida o no proporcionada'
            ], 401);
        }

        return $next($request);
    }
}
