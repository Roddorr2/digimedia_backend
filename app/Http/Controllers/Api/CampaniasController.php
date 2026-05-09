<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CampaniaWhatsApp;
use App\Models\WatModal;
use App\Models\modalservicios;
use Illuminate\Support\Facades\Http;

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

    $query = $campania->mensajesWhatsApp()
        ->with('modalServicio');

    // 🔹 FILTRO ESTADO
    if ($request->filled('estado')) {

        if ($request->estado === 'pendiente') {

            $query->whereNull('estado');
        }

        if ($request->estado === 'enviado') {

            $query->where('estado', 1);
        }

        if ($request->estado === 'fallido') {

            $query->where('estado', 0);
        }
    }

    // 🔹 FILTRO SEARCH
    if ($request->filled('search')) {

        $search = $request->search;

        $query->whereHas('modalServicio', function ($q) use ($search) {

            $q->where('nombre', 'like', "%{$search}%")
              ->orWhere('telefono', 'like', "%{$search}%");
        });
    }

    $leads = $query
        ->orderByRaw('fecha IS NULL DESC')
        ->orderBy('fecha', 'desc')
        ->paginate($request->get('per_page', 15));

    $data = $leads->getCollection()->map(function ($lead) {

        return [

            'id_modalservicio' => $lead->id_modalservicio,

            'id_modal_wat' => $lead->id_modal_wat,

            'nombre' => optional($lead->modalServicio)->nombre,

            'telefono' => optional($lead->modalServicio)->telefono,

            'estado' => is_null($lead->estado)
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
