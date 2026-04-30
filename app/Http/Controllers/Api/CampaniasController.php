<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaniaWhatsApp;
use App\Models\modalservicios;
use App\Models\WatModal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampaniasController extends Controller
{
    /**
     * GET /api/campanias
     * Listado paginado de campañas con filtro por estado.
     */
    public function index(Request $request)
    {
        try {
            $query = CampaniaWhatsApp::query()
                ->join('servicios', 'campanias_whatsapp.id_servicio', '=', 'servicios.id_servicio')
                ->select(
                    'campanias_whatsapp.id_campania',
                    'servicios.nombre as servicio',
                    'campanias_whatsapp.estado',
                    'campanias_whatsapp.total_destinatarios',
                    'campanias_whatsapp.envios_exitosos',
                    'campanias_whatsapp.envios_fallidos',
                    'campanias_whatsapp.envios_pendientes',
                    'campanias_whatsapp.fecha_inicio',
                    'campanias_whatsapp.fecha_fin',
                    'campanias_whatsapp.created_at'
                )
                ->orderBy('campanias_whatsapp.created_at', 'desc');

            // Filtro opcional por estado
            if ($request->filled('estado')) {
                $query->where('campanias_whatsapp.estado', $request->estado);
            }

            $paginated = $query->paginate(15);

            // Calcular porcentaje para cada campaña
            $paginated->getCollection()->transform(function ($campania) {
                $campania->porcentaje = $campania->total_destinatarios > 0
                    ? round(($campania->envios_exitosos / $campania->total_destinatarios) * 100, 1)
                    : 0;
                return $campania;
            });

            // Campaña activa (si hay alguna en_proceso o pausada)
            $activeCampaign = CampaniaWhatsApp::join('servicios', 'campanias_whatsapp.id_servicio', '=', 'servicios.id_servicio')
                ->select(
                    'campanias_whatsapp.id_campania',
                    'servicios.nombre as servicio',
                    'campanias_whatsapp.estado',
                    'campanias_whatsapp.total_destinatarios',
                    'campanias_whatsapp.envios_exitosos',
                    'campanias_whatsapp.envios_fallidos',
                    'campanias_whatsapp.envios_pendientes',
                    'campanias_whatsapp.envios_hoy'
                )
                ->whereIn('campanias_whatsapp.estado', [
                    'en_proceso',
                    'pausada_hasta_mañana',
                    'pausada_fuera_horario',
                    'pausada_sin_conexion',
                ])
                ->orderBy('campanias_whatsapp.fecha_inicio', 'desc')
                ->first();

            if ($activeCampaign) {
                $activeCampaign->porcentaje = $activeCampaign->total_destinatarios > 0
                    ? round(($activeCampaign->envios_exitosos / $activeCampaign->total_destinatarios) * 100, 1)
                    : 0;
            }

            return response()->json([
                'success' => true,
                'data' => $paginated,
                'active_campaign' => $activeCampaign,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el listado de campañas.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/campanias/{id}
     * Detalle completo de una campaña.
     */
    public function show($id)
    {
        try {
            $campania = CampaniaWhatsApp::join('servicios', 'campanias_whatsapp.id_servicio', '=', 'servicios.id_servicio')
                ->select(
                    'campanias_whatsapp.id_campania',
                    'servicios.nombre as servicio',
                    'campanias_whatsapp.id_servicio',
                    'campanias_whatsapp.estado',
                    'campanias_whatsapp.parrafo',
                    'campanias_whatsapp.imagen_url',
                    'campanias_whatsapp.total_destinatarios',
                    'campanias_whatsapp.envios_exitosos',
                    'campanias_whatsapp.envios_fallidos',
                    'campanias_whatsapp.envios_pendientes',
                    'campanias_whatsapp.envios_hoy',
                    'campanias_whatsapp.fecha_inicio',
                    'campanias_whatsapp.fecha_fin',
                    'campanias_whatsapp.created_at'
                )
                ->where('campanias_whatsapp.id_campania', $id)
                ->first();

            if (!$campania) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaña no encontrada.',
                ], 404);
            }

            $campania->porcentaje = $campania->total_destinatarios > 0
                ? round(($campania->envios_exitosos / $campania->total_destinatarios) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'data' => $campania,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el detalle de la campaña.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/campanias/{id}/leads
     * Tabla de leads de una campaña con filtros y paginación.
     *
     * Query params:
     *   ?estado=enviado|fallido|pendiente
     *   ?search=  (nombre o teléfono)
     *   ?page=
     */
    public function leads(Request $request, $id)
    {
        try {
            // Verificar que la campaña existe
            $campania = CampaniaWhatsApp::where('id_campania', $id)->first();
            if (!$campania) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaña no encontrada.',
                ], 404);
            }

            $estadoFiltro = $request->input('estado'); // enviado|fallido|pendiente
            $search       = $request->input('search');

            /**
             * LEFT JOIN desde modalservicios hacia modal_wats (para capturar leads pendientes).
             *
             * Los leads de la campaña son aquellos que:
             *   - Tienen un registro en modal_wats con campania_id = $id, O
             *   - Tienen id_servicio == $campania->id_servicio y aún no tienen registro (pendientes)
             *
             * Usamos la tabla modalservicios como base y LEFT JOIN con modal_wats
             * filtrando la campaña en el ON para no excluir leads sin registro.
             */
            $query = DB::table('modalservicios as ms')
                ->leftJoin('modal_wats as mw', function ($join) use ($id) {
                    $join->on('mw.id_modalservicio', '=', 'ms.id_modalservicio')
                         ->where('mw.campania_id', '=', $id);
                })
                ->where('ms.id_servicio', $campania->id_servicio)
                ->select(
                    'mw.id_modal_wat',
                    'ms.nombre',
                    'ms.telefono',
                    'mw.estado as estado_raw',
                    'mw.intentos',
                    'mw.puede_reintentar',
                    'mw.error',
                    'mw.fecha'
                );

            // Búsqueda por nombre o teléfono
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ms.nombre', 'like', "%{$search}%")
                      ->orWhere('ms.telefono', 'like', "%{$search}%");
                });
            }

            // Filtro por estado (mapeado)
            if ($estadoFiltro) {
                switch ($estadoFiltro) {
                    case 'enviado':
                        $query->where('mw.estado', 1)->whereNotNull('mw.id_modal_wat');
                        break;
                    case 'fallido':
                        $query->where('mw.estado', 0)->whereNotNull('mw.id_modal_wat');
                        break;
                    case 'pendiente':
                        $query->whereNull('mw.id_modal_wat');
                        break;
                }
            }

            $paginated = $query->paginate(20);

            // Mapear estado boolean → string legible
            $paginated->getCollection()->transform(function ($lead) {
                if (is_null($lead->id_modal_wat)) {
                    $lead->estado = 'pendiente';
                } elseif ($lead->estado_raw == 1) {
                    $lead->estado = 'enviado';
                } else {
                    $lead->estado = 'fallido';
                }

                // Limpiar campo raw que no debe exponerse al frontend
                unset($lead->estado_raw);

                // Castear tipos
                $lead->intentos        = $lead->intentos ? (int) $lead->intentos : 0;
                $lead->puede_reintentar = (bool) $lead->puede_reintentar;

                return $lead;
            });

            return response()->json([
                'success' => true,
                'data' => $paginated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los leads de la campaña.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/campanias/{id}/leads/{wat_modal_id}/retry
     * Reintento manual individual de un lead fallido.
     */
    public function retryLead(Request $request, $id, $wat_modal_id)
    {
        try {
            // 1. Obtener campaña
            $campania = CampaniaWhatsApp::where('id_campania', $id)->first();
            if (!$campania) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaña no encontrada.',
                ], 404);
            }

            // 2. Obtener el registro de envío del lead
            $watModal = WatModal::where('id_modal_wat', $wat_modal_id)
                ->where('campania_id', $id)
                ->first();

            if (!$watModal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro de lead no encontrado en esta campaña.',
                ], 404);
            }

            // 3. Verificar que el lead puede reintentarse
            if (!$watModal->canRetry()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lead no puede reintentarse (máximo 3 intentos alcanzado o ya fue enviado exitosamente).',
                ], 400);
            }

            // 4. Verificar horario permitido (8am–11pm Lima)
            if (!CampaniaWhatsApp::isWithinAllowedHours()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fuera del horario permitido para envíos (8:00am–11:00pm hora Lima).',
                ], 400);
            }

            // 5. Verificar cuota diaria de la campaña
            $campania->resetDailyCounterIfNeeded();
            if ($campania->getRemainingDailyQuota() <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se alcanzó el límite diario de envíos (50 mensajes/día). Intente mañana.',
                ], 400);
            }

            // 6. Obtener datos del lead desde modalservicios
            $modalServicio = modalservicios::where('id_modalservicio', $watModal->id_modalservicio)->first();
            if (!$modalServicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron los datos del lead.',
                ], 404);
            }

            // 7. Llamar al endpoint de reintento del whatsapp-service
            $payload = [
                'telefono'   => formatearTelefonoWhatsApp($modalServicio->telefono),
                'nombre'     => $modalServicio->nombre,
                'imagen_url' => $campania->imagen_url,
                'mensaje'    => $campania->parrafo,
            ];

            Log::info('Reintento manual de lead de campaña', [
                'campania_id'  => $id,
                'wat_modal_id' => $wat_modal_id,
                'telefono'     => $payload['telefono'],
            ]);

            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                    'X-API-Key'    => env('WHATSAPP_SERVICE_API_KEY'),
                ])
                ->post(env('WHATSAPP_API_URL') . '/api/whatsapp/send-campaign-retry', $payload);

            // 8. Actualizar BD en transacción atómica
            DB::transaction(function () use ($campania, $watModal, $response) {
                if ($response->successful() && $response->json('success') === true) {
                    // Éxito: marcar enviado, actualizar contadores
                    $watModal->update([
                        'estado'           => 1,
                        'error'            => null,
                        'intentos'         => $watModal->intentos + 1,
                        'puede_reintentar' => false,
                        'fecha'            => now(),
                    ]);

                    $campania->increment('envios_exitosos');
                    $campania->decrement('envios_fallidos');
                    $campania->increment('envios_hoy');
                    $campania->update(['fecha_ultimo_envio' => now()->toDateString()]);
                } else {
                    // Fallo: incrementar intentos, recalcular puede_reintentar
                    $nuevosIntentos = $watModal->intentos + 1;
                    $watModal->update([
                        'intentos'         => $nuevosIntentos,
                        'error'            => $response->json('message') ?? 'Error en reintento manual',
                        'puede_reintentar' => $nuevosIntentos < 3,
                    ]);
                }
            });

            if ($response->successful() && $response->json('success') === true) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mensaje reenviado correctamente.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo reenviar el mensaje: ' . ($response->json('message') ?? 'Error en whatsapp-service'),
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error en retryLead', [
                'campania_id'  => $id,
                'wat_modal_id' => $wat_modal_id,
                'error'        => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno al procesar el reintento.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
