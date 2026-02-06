<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppCampaignJob;
use App\Models\CampaniaWhatsApp;
use App\Models\modalservicios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class WhatsAppCampaignController extends Controller
{
    /**
     * Mapeo de códigos de servicio a IDs de base de datos
     */
    private const SERVICE_MAP = [
        'p1' => 1, // Diseño y Desarrollo Web
        'p2' => 2, // Gestión de Redes Sociales
        'p3' => 3, // Marketing y Gestión Digital
        'p4' => 4, // Branding y Diseño
    ];

    /**
     * Activa una campaña de WhatsApp masiva
     */
    public function activateCampaign(Request $request)
    {
        try {
            // Validar datos de entrada
            $validated = $request->validate([
                'service' => 'required|string|in:p1,p2,p3,p4',
                'paragraph' => 'required|string|min:10|max:1000',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB
            ]);

            // Mapear servicio a ID
            $id_servicio = self::SERVICE_MAP[$validated['service']];

            // Verificar que hay destinatarios disponibles
            $destinatarios = $this->getDestinatarios($id_servicio);

            if ($destinatarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay destinatarios activos con teléfonos válidos para este servicio'
                ], 400);
            }

            // Prevenir campañas duplicadas recientes
            $campaniaReciente = CampaniaWhatsApp::where('id_servicio', $id_servicio)
                ->where('created_at', '>', now()->subMinutes(5))
                ->whereIn('estado', ['pendiente', 'en_proceso'])
                ->first();

            if ($campaniaReciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una campaña en proceso para este servicio. Espera 5 minutos.',
                    'campania_id' => $campaniaReciente->id_campania
                ], 409);
            }

            // Subir imagen a Cloudinary
            $imagenUrl = $this->uploadImageToCloudinary($request->file('image'));

            if (!$imagenUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir la imagen a Cloudinary'
                ], 500);
            }

            // Crear registro de campaña
            $campania = CampaniaWhatsApp::create([
                'id_servicio' => $id_servicio,
                'parrafo' => $validated['paragraph'],
                'imagen_url' => $imagenUrl,
                'estado' => 'pendiente',
                'total_destinatarios' => $destinatarios->count(),
                'envios_pendientes' => $destinatarios->count(),
                'fecha_inicio' => now(),
            ]);

            // Despachar Job para envío en chunks
            SendWhatsAppCampaignJob::dispatch($campania, $destinatarios->toArray());

            Log::info('Campaña WhatsApp creada', [
                'campania_id' => $campania->id_campania,
                'servicio' => $validated['service'],
                'total_destinatarios' => $destinatarios->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Campaña iniciada exitosamente',
                'data' => [
                    'campania_id' => $campania->id_campania,
                    'total_destinatarios' => $campania->total_destinatarios,
                    'estado' => $campania->estado,
                    'servicio' => $campania->servicio->nombre ?? 'Desconocido'
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al activar campaña WhatsApp', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el estado de una campaña
     */
    public function getCampaignStatus($id)
    {
        try {
            $campania = CampaniaWhatsApp::with('servicio')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id_campania' => $campania->id_campania,
                    'servicio' => $campania->servicio->nombre ?? 'Desconocido',
                    'estado' => $campania->estado,
                    'progreso' => [
                        'total' => $campania->total_destinatarios,
                        'exitosos' => $campania->envios_exitosos,
                        'fallidos' => $campania->envios_fallidos,
                        'pendientes' => $campania->envios_pendientes,
                        'porcentaje' => $campania->getProgressPercentage()
                    ],
                    'fechas' => [
                        'inicio' => $campania->fecha_inicio,
                        'fin' => $campania->fecha_fin,
                        'creacion' => $campania->created_at
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Campaña no encontrada'
            ], 404);
        }
    }

    /**
     * Lista las campañas recientes
     */
    public function listCampaigns(Request $request)
    {
        $limit = $request->get('limit', 10);

        $campanias = CampaniaWhatsApp::with('servicio')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $campanias->map(function ($campania) {
                return [
                    'id_campania' => $campania->id_campania,
                    'servicio' => $campania->servicio->nombre ?? 'Desconocido',
                    'estado' => $campania->estado,
                    'total_destinatarios' => $campania->total_destinatarios,
                    'envios_exitosos' => $campania->envios_exitosos,
                    'envios_fallidos' => $campania->envios_fallidos,
                    'porcentaje' => $campania->getProgressPercentage(),
                    'fecha_inicio' => $campania->fecha_inicio,
                    'fecha_fin' => $campania->fecha_fin,
                ];
            })
        ]);
    }

    /**
     * Obtiene destinatarios activos para un servicio
     */
    private function getDestinatarios($id_servicio)
    {
        return modalservicios::where('id_servicio', $id_servicio)
            ->where('estado', 1) // Solo activos
            ->whereNotNull('telefono')
            ->where('telefono', '!=', '')
            ->get()
            ->filter(function ($modal) {
                // Validar que el teléfono sea válido
                return validarTelefonoPeruano($modal->telefono);
            });
    }

    /**
     * Sube una imagen a Cloudinary
     */
    private function uploadImageToCloudinary($image)
    {
        try {
            $uploadedFile = Cloudinary::upload($image->getRealPath(), [
                'folder' => 'campanias_whatsapp',
                'transformation' => [
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]
            ]);

            return $uploadedFile->getSecurePath();
        } catch (\Exception $e) {
            Log::error('Error al subir imagen a Cloudinary', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
