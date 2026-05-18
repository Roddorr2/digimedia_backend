<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CampaniaWhatsApp;
use App\Models\WatModal;
use App\Models\modalservicios;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CampaniasController extends Controller
{
    // GET /api/campanias
    public function index(Request $request)
    {
        try {

            $query = CampaniaWhatsApp::with('servicio');

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            $campanias = $query
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // transformar SOLO items, NO romper estructura paginator
            $campanias->getCollection()->transform(function ($campania) {

                return [
                    'id_campania' => $campania->id_campania,
                   'servicio' => optional($campania->servicio)->nombre,
                    'estado' => $campania->estado,
                    'total_destinatarios' => $campania->total_destinatarios,
                    'envios_exitosos' => $campania->envios_exitosos,
                    'envios_fallidos' => $campania->envios_fallidos,
                    'envios_pendientes' => $campania->envios_pendientes,
                    'porcentaje' => $campania->getProgressPercentage(),
                    'fecha_inicio' => $campania->fecha_inicio,
                    'fecha_fin' => $campania->fecha_fin,
                    'created_at' => $campania->created_at,
                ];
            });

            $activeCampaign = CampaniaWhatsApp::getActiveCampaign();

            return response()->json([
                'success' => true,
                'data' => $campanias, // 👈 AQUÍ está la clave
                'active_campaign' => $activeCampaign
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener campañas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // GET /api/campanias/{id}
    public function show($id){
        try {

            $campania = CampaniaWhatsApp::with([
                'servicio',
                'usuario',
                'mensajesWhatsApp'
            ])->find($id);

            // No encontrada
            if (!$campania) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaña no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,

                'data' => [

                    'id_campania' => $campania->id_campania,

                    // Servicio
                    'servicio' => [
                        'id_servicio' => $campania->servicio->id_servicio ?? null,
                        'nombre' => $campania->servicio->nombre ?? null,
                    ],

                    // Usuario
                    'usuario' => [
                        'id' => $campania->usuario->id ?? null,
                        'name' => $campania->usuario->name ?? null,
                    ],

                    // Datos generales
                    'estado' => $campania->estado,
                    'parrafo' => $campania->parrafo,
                    'imagen_url' => $campania->imagen_url,

                    // Métricas
                    'total_destinatarios' => $campania->total_destinatarios,
                    'envios_exitosos' => $campania->envios_exitosos,
                    'envios_fallidos' => $campania->envios_fallidos,
                    'envios_pendientes' => $campania->envios_pendientes,
                    'envios_hoy' => $campania->envios_hoy,

                    // Progreso
                    'porcentaje_progreso' => $campania->getProgressPercentage(),

                    // Fechas
                    'fecha_inicio' => $campania->fecha_inicio,
                    'fecha_fin' => $campania->fecha_fin,
                    'fecha_ultimo_envio' => $campania->fecha_ultimo_envio,
                    'created_at' => $campania->created_at,

                    // Estados calculados
                    'is_completed' => $campania->isCompleted(),
                    'is_in_progress' => $campania->isInProgress(),
                    'is_draft' => $campania->isDraft(),
                    'is_paused_until_tomorrow' => $campania->isPausedUntilTomorrow(),
                    'is_paused_outside_hours' => $campania->isPausedOutsideHours(),

                    // Cuota diaria
                    'remaining_daily_quota' => $campania->getRemainingDailyQuota(),
                    'has_reached_daily_limit' => $campania->hasReachedDailyLimit(),

                    // Retry stats
                    'retry_stats' => $campania->getRetryStats(),

                    // Reintentos
                    'has_failed_messages_to_retry' => $campania->hasFailedMessagesToRetry(),

                    // Total mensajes asociados
                    'total_mensajes' => $campania->mensajesWhatsApp->count(),

                    // Auto resume
                    'can_auto_resume' => $campania->canAutoResume(),
                ]
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener campaña',
                'error' => $e->getMessage()
            ], 500);
        }
    }    

    // GET /api/campanias/{id}/leads
    public function leads(Request $request, $id)
    {
        $campania = CampaniaWhatsApp::findOrFail($id);

        // Leads válidos para la campaña:
        // - mismo servicio
        // - existentes antes de iniciar campaña
        // - solo activos
        // - teléfono válido
        // - SIN teléfonos duplicados (mismo criterio usado al crear campaña)
        $telefonosValidos = modalservicios::query()

            ->where('id_servicio', $campania->id_servicio)

            ->where('estado', 1)

            ->whereNotNull('telefono')

            ->where('telefono', '!=', '')

            ->where(
                'modalservicios.fecha',
                '<=',
                \Carbon\Carbon::parse($campania->fecha_inicio)
                    ->setTimezone('America/Lima')
                    ->format('Y-m-d H:i:s')
            )

            ->orderBy('id_modalservicio')

            ->get()

            ->unique('telefono')

            ->pluck('id_modalservicio');

        $query = modalservicios::query()

            ->whereIn('modalservicios.id_modalservicio', $telefonosValidos)

            ->leftJoin('modal_wats', function ($join) use ($id) {

                $join->on(
                    'modalservicios.id_modalservicio',
                    '=',
                    'modal_wats.id_modalservicio'
                )

                ->where('modal_wats.campania_id', $id);
            })

            ->select([
                'modalservicios.id_modalservicio',
                'modalservicios.nombre',
                'modalservicios.telefono',

                'modal_wats.id_modal_wat',
                'modal_wats.estado',
                'modal_wats.intentos',
                'modal_wats.puede_reintentar',
                'modal_wats.error',
                'modal_wats.fecha',
            ]);

        // 🔹 FILTRO ESTADO
        if ($request->filled('estado')) {

            if ($request->estado === 'pendiente') {

                $query->whereNull('modal_wats.id_modal_wat');
            }

            if ($request->estado === 'enviado') {

                $query->where('modal_wats.estado', 1);
            }

            if ($request->estado === 'fallido') {

                $query->where('modal_wats.estado', 0);
            }
        }

        // 🔹 FILTRO SEARCH
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('modalservicios.nombre', 'like', "%{$search}%")
                    ->orWhere('modalservicios.telefono', 'like', "%{$search}%");
            });
        }

        $leads = $query

            // pendientes primero
            ->orderByRaw('modal_wats.id_modal_wat IS NULL DESC')

            // luego más recientes
            ->orderBy('modal_wats.fecha', 'desc')

            ->paginate($request->get('per_page', 15));

        $data = $leads->getCollection()->map(function ($lead) {

            return [

                'id_modalservicio' => $lead->id_modalservicio,

                'id_modal_wat' => $lead->id_modal_wat,

                'nombre' => $lead->nombre,

                'telefono' => $lead->telefono,

                'estado' => is_null($lead->id_modal_wat)
                    ? 'pendiente'
                    : ($lead->estado ? 'enviado' : 'fallido'),

                'intentos' => $lead->intentos ?? 0,

                'puede_reintentar' => (bool) ($lead->puede_reintentar ?? false),

                'error' => $lead->error,

                'fecha' => $lead->fecha
                    ? \Carbon\Carbon::parse($lead->fecha)
                        ->format('Y-m-d\TH:i:s')
                    : null,
            ];
        });

        return response()->json([
            'success' => true,

            'data' => [
                'data' => $data,
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'total' => $leads->total(),
            ]
        ]);
    }

    // POST /api/campanias/{id}/leads/{wat_modal_id}/retry
    public function retryLead(Request $request, $id, $wat_modal_id) 
    {
        // 1. Obtener campaña y verificar que existe
        $campania = CampaniaWhatsApp::findOrFail($id);

        // 2. Obtener el registro WatModal
        $watModal = WatModal::where('id_modal_wat', $wat_modal_id)
            ->where('campania_id', $id)
            ->firstOrFail();

        // 3. Verificar que el lead puede reintentarse
        if (!$watModal->canRetry()) {
            return response()->json([
                'success' => false,
                'message' => 'Este lead no puede reintentarse (máximo 3 intentos o ya fue enviado)'
            ], 400);
        }

        // 4. Verificar horario permitido (8am–11pm Lima)
        if (!CampaniaWhatsApp::isWithinAllowedHours()) {
            return response()->json([
                'success' => false,
                'message' => 'Fuera del horario permitido (8am–11pm hora Lima)'
            ], 400);
        }
        
        if ($campania->hasReachedDailyLimit()) {
            return response()->json([
                'success' => false,
                'message' => 'La campaña alcanzó el límite diario de envíos'
            ], 400);
        }

        // 5. Obtener datos del lead desde modalservicios
        $modalServicio = modalservicios::findOrFail($watModal->id_modalservicio);

        // 6. Llamar al whatsapp-service con imagen + párrafo de la campaña
        $response = Http::timeout(60)
            ->withHeaders(['X-API-Key' => env('WHATSAPP_SERVICE_API_KEY')])
            ->post(env('WHATSAPP_API_URL') . '/api/whatsapp/send-message', [
                'telefono'   => formatearTelefonoWhatsApp($modalServicio->telefono),
                'mensaje'    => $campania->parrafo,
                'imagen_url' => $campania->imagen_url,
            ]);

        // 7. Actualizar WatModal y contadores de la campaña
        if ($response->successful() && $response->json('success')) {
            $watModal->update([
                'estado'          => 1,
                'error'           => null,
                'intentos'        => $watModal->intentos + 1,
                'puede_reintentar'=> false,
                'fecha'           => now(),
            ]);
            $campania->increment('envios_exitosos');
            $campania->increment('envios_hoy');
            $campania->decrement('envios_fallidos');

            return response()->json(['success' => true, 'message' => 'Mensaje reenviado correctamente']);
        }

        // 8. Si falló, incrementar intentos
        $newIntentos = $watModal->intentos + 1;
        $watModal->update([
            'estado'           => 0,
            'intentos'         => $newIntentos,
            'error'            => $response->json('error') ?? 'Error en reintento manual',
            'puede_reintentar' => $newIntentos < 3,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'No se pudo reenviar el mensaje'
        ], 500);
    }
}
