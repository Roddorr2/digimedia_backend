<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppExternalService
{
    public function sendMessage(string $telefono, string $mensaje, ?string $imagenUrl): array
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders(['X-API-Key' => config('services.whatsapp.api_key')])
                ->post(config('services.whatsapp.api_url') . '/api/whatsapp/send-message', [
                    'telefono' => $telefono,
                    'mensaje' => $mensaje,
                    'imagen_url' => $imagenUrl,
                ]);

            $success = $response->successful() && $response->json('success');
            
            return [
                'success' => $success,
                'error' => $success ? null : ($response->json('error') ?? 'Error desconocido')
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp API error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}