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
     * Crea una campaña de WhatsApp en estado BORRADOR
     */
    public function createCampaign(Request $request)
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

            // Subir imagen a Cloudinary
            $imagenUrl = $this->uploadImageToCloudinary($request->file('image'));

            if (!$imagenUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir la imagen a Cloudinary'
                ], 500);
            }

            // Crear registro de campaña en BORRADOR con auditoría de usuario
            $campania = CampaniaWhatsApp::create([
                'id_servicio' => $id_servicio,
                'user_id' => $request->user()->id,
                'parrafo' => $validated['paragraph'],
                'imagen_url' => $imagenUrl,
                'estado' => 'borrador',
                'total_destinatarios' => $destinatarios->count(),
                'envios_pendientes' => $destinatarios->count(),
            ]);

            Log::info('Campaña WhatsApp creada en borrador', [
                'campania_id' => $campania->id_campania,
                'servicio' => $validated['service'],
                'total_destinatarios' => $destinatarios->count(),
                'creado_por_user_id' => $request->user()->id,
                'creado_por_nombre' => $request->user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Campaña creada exitosamente en borrador',
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
            Log::error('Error al crear campaña WhatsApp', [
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
     * Inicia una campaña desde BORRADOR o PAUSADA (con validación FIFO)
     */
    public function startCampaign(Request $request, $id)
    {
        try {
            $campania = CampaniaWhatsApp::findOrFail($id);

            // 🔒 VALIDACIÓN 1: Verificar que WhatsApp está conectado
            $whatsappStatus = $this->checkWhatsAppConnection();
            if (!$whatsappStatus['connected']) {
                return response()->json([
                    'success' => false,
                    'message' => '📱 WhatsApp no está conectado. Por favor, escanea el código QR en la pestaña "Conexión" primero.',
                    'error_type' => 'whatsapp_not_connected',
                    'details' => $whatsappStatus['message'] ?? 'Servicio WhatsApp no disponible'
                ], 400);
            }

            // 🔒 VALIDACIÓN 2 (FIFO): Verificar que no hay otra campaña activa
            if (!$campania->canBeStarted()) {
                $activeCampaign = CampaniaWhatsApp::getActiveCampaign();
                
                if ($activeCampaign && $activeCampaign->id_campania !== $campania->id_campania) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya hay una campaña en proceso. Espera a que finalice.',
                        'error_type' => 'campaign_active',
                        'active_campaign' => [
                            'id' => $activeCampaign->id_campania,
                            'servicio' => $activeCampaign->servicio->nombre ?? 'Desconocido',
                            'estado' => $activeCampaign->estado,
                            'progreso' => $activeCampaign->getProgressPercentage() . '%'
                        ]
                    ], 409);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'La campaña no puede ser iniciada. Estado actual: ' . $campania->estado,
                    'error_type' => 'invalid_state'
                ], 400);
            }

            // Obtener destinatarios pendientes
            $destinatarios = $this->getDestinatarios($campania->id_servicio);

            if ($destinatarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay destinatarios disponibles para esta campaña'
                ], 400);
            }

            // Actualizar estado y fecha de inicio
            $campania->update([
                'estado' => 'pendiente',
                'fecha_inicio' => now()
            ]);

            // 🚀 Despachar Job para envío
            SendWhatsAppCampaignJob::dispatch($campania, $destinatarios->toArray());

            Log::info('Campaña WhatsApp iniciada', [
                'campania_id' => $campania->id_campania,
                'iniciada_por_user_id' => $request->user()->id,
                'iniciada_por_nombre' => $request->user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Campaña iniciada exitosamente',
                'data' => [
                    'campania_id' => $campania->id_campania,
                    'estado' => $campania->estado,
                    'total_destinatarios' => $campania->total_destinatarios
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al iniciar campaña WhatsApp', [
                'campania_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar campaña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Devuelve el total de destinatarios para un servicio (sin crear campaña)
     */
    public function previewCampaign(Request $request, $service)
    {
        if (!array_key_exists($service, self::SERVICE_MAP)) {
            return response()->json([
                'success' => false,
                'message' => 'Servicio inválido. Usa p1, p2, p3 o p4.'
            ], 422);
        }

        $id_servicio = self::SERVICE_MAP[$service];
        $destinatarios = $this->getDestinatarios($id_servicio);

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $service,
                'total_destinatarios' => $destinatarios->count(),
            ]
        ]);
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
                    'envios_hoy' => $campania->envios_hoy ?? 0,
                    'limite_diario' => $campania->limite_diario ?? 50,
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
     * Lista las campañas recientes con info de campaña activa
     */
    public function listCampaigns(Request $request)
    {
        $limit = $request->get('limit', 10);

        $campanias = CampaniaWhatsApp::with('servicio')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Obtener campaña activa para UI
        $activeCampaign = CampaniaWhatsApp::getActiveCampaign();

        return response()->json([
            'success' => true,
            'active_campaign' => $activeCampaign ? [
                'id_campania' => $activeCampaign->id_campania,
                'servicio' => $activeCampaign->servicio->nombre ?? 'Desconocido',
                'estado' => $activeCampaign->estado,
                'progreso' => $activeCampaign->getProgressPercentage(),
                'envios_hoy' => $activeCampaign->envios_hoy,
            ] : null,
            'data' => [
                'campanias' => $campanias->map(function ($campania) {
                    return [
                        'id_campania' => $campania->id_campania,
                        'servicio' => $campania->servicio->nombre ?? 'Desconocido',
                        'estado' => $campania->estado,
                        'total_destinatarios' => $campania->total_destinatarios,
                        'envios_exitosos' => $campania->envios_exitosos,
                        'envios_fallidos' => $campania->envios_fallidos,
                        'envios_pendientes' => $campania->envios_pendientes,
                        'envios_hoy' => $campania->envios_hoy,
                        'porcentaje' => $campania->getProgressPercentage(),
                        'fecha_inicio' => $campania->fecha_inicio,
                        'fecha_fin' => $campania->fecha_fin,
                        'fecha_ultimo_envio' => $campania->fecha_ultimo_envio,
                        'can_be_started' => $campania->canBeStarted(),
                        'created_at' => $campania->created_at,
                    ];
                })
            ]
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
            ->orderBy('id_modalservicio') // Priorizar el registro más antiguo en caso de duplicados
            ->get()
            ->unique('telefono') // Enviar solo un mensaje por número de teléfono
            ->values();
    }

    /**
     * TEST: Endpoint para probar subida de imágenes a Cloudinary
     * URL: POST /api/test-cloudinary
     */
    public function testCloudinaryUpload(Request $request)
    {
        try {
            // Validar que se envió una imagen
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120'
            ]);

            $image = $request->file('image');
            
            // Subir a Cloudinary
            $result = Cloudinary::uploadApi()->upload($image->getRealPath(), [
                'folder' => 'test_uploads'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen subida exitosamente a Cloudinary',
                'data' => [
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id'],
                    'format' => $result['format'],
                    'width' => $result['width'],
                    'height' => $result['height'],
                    'size_bytes' => $result['bytes'],
                    'created_at' => $result['created_at']
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo inválido',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en test de Cloudinary', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al subir imagen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sube una imagen a Cloudinary
     */
    private function uploadImageToCloudinary($image)
    {
        try {
            $result = Cloudinary::uploadApi()->upload($image->getRealPath(), [
                'folder' => 'campanias_whatsapp'
            ]);
            
            return $result['secure_url'];
            
        } catch (\Exception $e) {
            Log::error('Error al subir imagen a Cloudinary', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Verifica si el servicio WhatsApp está conectado y listo
     */
    private function checkWhatsAppConnection(): array
    {
        try {
            $whatsappServiceUrl = env('WHATSAPP_API_URL', 'http://localhost:5111');
            $apiKey = env('WHATSAPP_SERVICE_API_KEY');

            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->withHeaders(['X-API-Key' => $apiKey])
                ->get($whatsappServiceUrl . '/api/whatsapp/status');

            if ($response->successful()) {
                $data = $response->json();

                $isConnected = ($data['connected'] ?? false) === true;

                return [
                    'connected' => $isConnected,
                    'message' => $isConnected ? 'WhatsApp conectado' : 'El servicio WhatsApp está disponible pero no conectado a WhatsApp',
                    'status' => $data
                ];
            }

            return [
                'connected' => false,
                'message' => 'Servicio WhatsApp no responde correctamente (HTTP ' . $response->status() . ')',
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::warning('No se pudo verificar conexión WhatsApp', [
                'error' => $e->getMessage()
            ]);

            return [
                'connected' => false,
                'message' => 'No se pudo conectar al servicio WhatsApp: ' . $e->getMessage()
            ];
        }
    }
}

