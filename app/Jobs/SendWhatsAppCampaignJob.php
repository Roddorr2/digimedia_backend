<?php

namespace App\Jobs;

use App\Models\CampaniaWhatsApp;
use App\Models\WatModal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campania;
    public $destinatarios;
    public $tries = 3;
    public $timeout = 900; // 15 minutos por Job (para chunks grandes con reintentos)

    /**
     * Constructor
     * @param CampaniaWhatsApp $campania
     * @param array $destinatarios Array con los registros de modalservicios
     */
    public function __construct(CampaniaWhatsApp $campania, array $destinatarios)
    {
        $this->campania = $campania;
        $this->destinatarios = $destinatarios;
    }

    /**
     * Ejecuta el Job
     */
    public function handle()
    {
        try {
            Log::info('Iniciando campaña WhatsApp', [
                'campania_id' => $this->campania->id_campania,
                'total_destinatarios' => count($this->destinatarios)
            ]);

            // Resetear contador diario si es un nuevo día
            $this->campania->resetDailyCounterIfNeeded();

            // Actualizar estado de la campaña
            $this->campania->update(['estado' => 'en_proceso']);

            // Dividir destinatarios en chunks de 20 (límite diario considerado)
            $chunks = array_chunk($this->destinatarios, 20);
            $totalChunks = count($chunks);

            Log::info('Campaña dividida en chunks', [
                'campania_id' => $this->campania->id_campania,
                'total_chunks' => $totalChunks,
                'chunk_size' => 20
            ]);

            // Procesar cada chunk
            foreach ($chunks as $index => $chunk) {
                $chunkNumber = $index + 1;
                
                // VERIFICAR VENTANA HORARIA (8am-11pm Peru GMT-5)
                if (!CampaniaWhatsApp::isWithinAllowedHours()) {
                    $nextStart = CampaniaWhatsApp::getNextStartTime();
                    
                    Log::info('Fuera de horario permitido - pausando campaña', [
                        'campania_id' => $this->campania->id_campania,
                        'hora_actual' => now()->timezone('America/Lima')->format('H:i'),
                        'proxima_reanudacion' => $nextStart->format('Y-m-d H:i'),
                        'chunks_restantes' => $totalChunks - $index
                    ]);
                    
                    // Pausar campaña fuera de horario
                    $this->campania->update(['estado' => 'pausada_fuera_horario']);
                    break; // Salir del loop
                }
                
                // VERIFICAR LÍMITE DIARIO ANTES DE PROCESAR CHUNK
                $this->campania->refresh(); // Actualizar datos desde DB
                $remainingQuota = $this->campania->getRemainingDailyQuota();
                
                if ($remainingQuota <= 0) {
                    Log::info('Límite diario alcanzado - pausando campaña', [
                        'campania_id' => $this->campania->id_campania,
                        'envios_hoy' => $this->campania->envios_hoy,
                        'chunks_restantes' => $totalChunks - $index
                    ]);
                    
                    // Pausar campaña hasta mañana
                    $this->campania->update(['estado' => 'pausada_hasta_mañana']);
                    break; // Salir del loop
                }
                
                // Ajustar chunk si excede el límite diario
                $chunkedRecipients = $chunk;
                if (count($chunk) > $remainingQuota) {
                    $chunkedRecipients = array_slice($chunk, 0, $remainingQuota);
                    Log::info('Chunk ajustado por límite diario', [
                        'campania_id' => $this->campania->id_campania,
                        'original_size' => count($chunk),
                        'adjusted_size' => count($chunkedRecipients),
                        'remaining_quota' => $remainingQuota
                    ]);
                }
                
                Log::info("Procesando chunk {$chunkNumber}/{$totalChunks}", [
                    'campania_id' => $this->campania->id_campania,
                    'destinatarios_en_chunk' => count($chunkedRecipients),
                    'envios_hoy_actual' => $this->campania->envios_hoy
                ]);

                $this->processChunk($chunkedRecipients, $chunkNumber);

                // Verificar si la campaña fue pausada por desconexión durante el processChunk
                $this->campania->refresh();
                if ($this->campania->estado === 'pausada_sin_conexion') {
                    Log::info('Campaña pausada por desconexión - deteniendo procesamiento de chunks restantes', [
                        'campania_id' => $this->campania->id_campania,
                        'chunks_restantes' => $totalChunks - $chunkNumber
                    ]);
                    break; // Salir del loop de chunks
                }

                // PAUSA DE 2 MINUTOS ENTRE CHUNKS
                if ($chunkNumber < $totalChunks) {
                    sleep(120); // 2 minutos
                }
            }

            // 🔁 FASE 3: Procesar reintentos para mensajes fallidos
            // NO procesar reintentos si la campaña fue pausada
            $this->campania->refresh();
            if (in_array($this->campania->estado, ['pausada_sin_conexion', 'pausada_hasta_mañana', 'pausada_fuera_horario'])) {
                Log::info('Campaña pausada - omitiendo procesamiento de reintentos', [
                    'campania_id' => $this->campania->id_campania,
                    'estado' => $this->campania->estado
                ]);
            } else {
                $this->processRetries();
            }

            // Finalizar campaña
            $this->campania->updateStatus();

            Log::info('Campaña WhatsApp finalizada', [
                'campania_id' => $this->campania->id_campania,
                'estado' => $this->campania->estado,
                'exitosos' => $this->campania->envios_exitosos,
                'fallidos' => $this->campania->envios_fallidos
            ]);

        } catch (\Exception $e) {
            Log::error('Error en SendWhatsAppCampaignJob', [
                'campania_id' => $this->campania->id_campania,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->campania->update(['estado' => 'error']);
            throw $e;
        }
    }

    /**
     * Procesa un chunk de destinatarios
     */
    private function processChunk(array $chunk, int $chunkNumber)
    {
        try {
            // Preparar payload para whatsapp-service
            $recipients = array_map(function ($destinatario) {
                return [
                    'id_modalservicio' => $destinatario['id_modalservicio'],
                    'nombre' => $destinatario['nombre'],
                    'telefono' => formatearTelefonoWhatsApp($destinatario['telefono']),
                ];
            }, $chunk);

            $payload = [
                'campania_id' => $this->campania->id_campania,
                'chunk_number' => $chunkNumber,
                'recipients' => $recipients,
                'message' => $this->campania->parrafo,
                'image_url' => $this->campania->imagen_url,
                'id_servicio' => $this->campania->id_servicio,
            ];

            // Enviar chunk a whatsapp-service
            // Timeout de 1800s (30 min) para permitir envío de chunks con delays largos
            // 20 mensajes × ~45s promedio = ~900s (15 min) + margen de seguridad
            $response = Http::timeout(1800)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-API-Key' => env('WHATSAPP_SERVICE_API_KEY'),
                ])
                ->post(env('WHATSAPP_API_URL') . '/api/whatsapp/send-campaign-batch', $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info("📊 Respuesta del whatsapp-service", [
                    'campania_id' => $this->campania->id_campania,
                    'chunk' => $chunkNumber,
                    'response' => $result
                ]);
                
                // ⚡ DETECCIÓN INMEDIATA: whatsapp-service detectó desconexión en tiempo real
                if (isset($result['paused']) && $result['paused'] === true) {
                    $exitosos = $result['successful'] ?? 0;
                    
                    Log::warning("⚡ WhatsApp desconectado DURANTE chunk - pausando inmediatamente", [
                        'campania_id' => $this->campania->id_campania,
                        'chunk_number' => $chunkNumber,
                        'exitosos_antes_desconexion' => $exitosos,
                        'pause_reason' => $result['pause_reason'] ?? 'unknown'
                    ]);
                    
                    // Registrar solo los exitosos antes de la desconexión
                    if ($exitosos > 0) {
                        $this->campania->increment('envios_exitosos', $exitosos);
                        $this->campania->decrement('envios_pendientes', $exitosos);
                        $this->campania->increment('envios_hoy', $exitosos);
                        
                        // Registrar en WatModal solo los exitosos
                        foreach ($chunk as $destinatario) {
                            $resultado = $result['results'][$destinatario['id_modalservicio']] ?? null;
                            if ($resultado && isset($resultado['success']) && $resultado['success']) {
                                WatModal::create([
                                    'id_modalservicio' => $destinatario['id_modalservicio'],
                                    'number_message' => 1,
                                    'estado' => 1,
                                    'error' => null,
                                    'fecha' => now(),
                                    'campania_id' => $this->campania->id_campania,
                                    'intentos' => 1,
                                    'puede_reintentar' => false,
                                ]);
                            }
                        }
                    }
                    
                    // Pausar campaña inmediatamente
                    $this->campania->update([
                        'estado' => 'pausada_sin_conexion',
                        'fecha_ultimo_envio' => now()->toDateString()
                    ]);
                    
                    return; // Detener procesamiento inmediatamente
                }
                
                // Actualizar contadores según respuesta
                $exitosos = $result['successful'] ?? 0;
                $fallidos = $result['failed'] ?? 0;
                
                // 🔌 DETECCIÓN DE DESCONEXIÓN DURANTE ENVÍO DE CHUNK
                // Si hay muchos fallos con error de conexión, pausar la campaña
                if ($fallidos > 0 && isset($result['results'])) {
                    $erroresConexion = 0;
                    $totalResultados = count($result['results']);
                    
                    foreach ($result['results'] as $resultado) {
                        if (isset($resultado['error']) && 
                            (str_contains($resultado['error'], 'Cannot read properties of undefined') ||
                             str_contains($resultado['error'], 'not connected') ||
                             str_contains(strtolower($resultado['error']), 'no está conectado'))) {
                            $erroresConexion++;
                        }
                    }
                    
                    // Si más del 80% de los mensajes fallaron por desconexión, pausar
                    $porcentajeErrorConexion = ($erroresConexion / $totalResultados) * 100;
                    
                    if ($porcentajeErrorConexion > 80) {
                        Log::warning("WhatsApp se desconectó durante envío - pausando campaña", [
                            'campania_id' => $this->campania->id_campania,
                            'chunk_number' => $chunkNumber,
                            'exitosos_antes_desconexion' => $exitosos,
                            'fallidos_por_desconexion' => $erroresConexion,
                            'porcentaje_error' => round($porcentajeErrorConexion, 2)
                        ]);
                        
                        // Marcar solo los que realmente fallaron por desconexión como reintentos
                        // Los exitosos ya se procesaron
                        $this->campania->increment('envios_exitosos', $exitosos);
                        $this->campania->decrement('envios_pendientes', $exitosos);
                        $this->campania->increment('envios_hoy', $exitosos);
                        $this->campania->update([
                            'estado' => 'pausada_sin_conexion',
                            'fecha_ultimo_envio' => now()->toDateString()
                        ]);
                        
                        // Registrar solo los exitosos (los fallidos quedan pendientes)
                        foreach ($chunk as $destinatario) {
                            $resultado = $result['results'][$destinatario['id_modalservicio']] ?? null;
                            if ($resultado && isset($resultado['success']) && $resultado['success']) {
                                WatModal::create([
                                    'id_modalservicio' => $destinatario['id_modalservicio'],
                                    'number_message' => 1,
                                    'estado' => 1,
                                    'error' => null,
                                    'fecha' => now(),
                                    'campania_id' => $this->campania->id_campania,
                                    'intentos' => 1,
                                    'puede_reintentar' => false,
                                ]);
                            }
                        }
                        
                        return; // Detener procesamiento
                    }
                }

                Log::info("🔄 Actualizando contadores", [
                    'campania_id' => $this->campania->id_campania,
                    'exitosos' => $exitosos,
                    'fallidos' => $fallidos,
                    'antes_exitosos' => $this->campania->envios_exitosos,
                    'antes_fallidos' => $this->campania->envios_fallidos,
                ]);

                $this->campania->increment('envios_exitosos', $exitosos);
                $this->campania->increment('envios_fallidos', $fallidos);
                $this->campania->decrement('envios_pendientes', $exitosos + $fallidos);
                
                // ACTUALIZAR CONTADOR DIARIO
                $this->campania->increment('envios_hoy', $exitosos + $fallidos);
                $this->campania->update(['fecha_ultimo_envio' => now()->toDateString()]);
                
                // Refrescar para obtener valores actuales
                $this->campania->refresh();
                
                Log::info("✅ Contadores actualizados", [
                    'campania_id' => $this->campania->id_campania,
                    'despues_exitosos' => $this->campania->envios_exitosos,
                    'despues_fallidos' => $this->campania->envios_fallidos,
                    'envios_hoy' => $this->campania->envios_hoy,
                ]);

                // Registrar en modal_wats
                foreach ($chunk as $destinatario) {
                    $estado = isset($result['results'][$destinatario['id_modalservicio']]) &&
                              $result['results'][$destinatario['id_modalservicio']]['success'] ? 1 : 0;

                    WatModal::create([
                        'id_modalservicio' => $destinatario['id_modalservicio'],
                        'number_message' => 1,
                        'estado' => $estado,
                        'error' => $estado ? null : ($result['results'][$destinatario['id_modalservicio']]['error'] ?? 'Error desconocido'),
                        'fecha' => now(),
                        'campania_id' => $this->campania->id_campania, // 🔁 FASE 3
                        'intentos' => 1, // 🔁 FASE 3: Primer intento
                        'puede_reintentar' => !$estado, // 🔁 FASE 3: Si falló, puede reintentar
                    ]);
                }

                Log::info("Chunk {$chunkNumber} procesado", [
                    'campania_id' => $this->campania->id_campania,
                    'exitosos' => $exitosos,
                    'fallidos' => $fallidos,
                    'envios_hoy_total' => $this->campania->envios_hoy
                ]);

            } else {
                // Si falla el request (no es 2xx), verificar el tipo de error
                $responseBody = $response->body();
                $responseStatus = $response->status();

                Log::error("Chunk {$chunkNumber} falló completamente", [
                    'campania_id' => $this->campania->id_campania,
                    'status' => $responseStatus,
                    'body' => $responseBody
                ]);

                // 🔌 DETECCIÓN DE DESCONEXIÓN DE WHATSAPP
                // Si el error es 500 y contiene "no está conectado", pausar en lugar de marcar como error
                if ($responseStatus === 500 && 
                    (str_contains(strtolower($responseBody), 'no está conectado') || 
                     str_contains(strtolower($responseBody), 'not connected'))) {
                    
                    Log::warning("WhatsApp desconectado - pausando campaña automáticamente", [
                        'campania_id' => $this->campania->id_campania,
                        'chunk_number' => $chunkNumber,
                        'mensaje' => 'Campaña se reanudará automáticamente cuando WhatsApp se reconecte'
                    ]);

                    // Pausar campaña con nuevo estado
                    $this->campania->update(['estado' => 'pausada_sin_conexion']);

                    // NO marcar mensajes como fallidos, quedarán pendientes para reintento
                    // El Cron Job los reanudará automáticamente cuando detecte conexión

                    // Retornar para detener procesamiento de este chunk
                    // El loop principal detectará el cambio de estado y detendrá chunks restantes
                    return;
                }

                // Si es otro tipo de error (no es desconexión), marcar como error
                $this->campania->update(['estado' => 'error']);

                // Marcar todos como fallidos
                $this->campania->increment('envios_fallidos', count($chunk));
                $this->campania->decrement('envios_pendientes', count($chunk));

                foreach ($chunk as $destinatario) {
                    WatModal::create([
                        'id_modalservicio' => $destinatario['id_modalservicio'],
                        'number_message' => 1,
                        'estado' => 0,
                        'error' => 'Error en request a whatsapp-service: ' . $responseStatus,
                        'fecha' => now(),
                        'campania_id' => $this->campania->id_campania, // 🔁 FASE 3
                        'intentos' => 1, // 🔁 FASE 3
                        'puede_reintentar' => true, // 🔁 FASE 3
                    ]);
                }
            }

        } catch (\Exception $e) {
            // Manejo de errores del chunk
            $errorMessage = $e->getMessage();
            
            // Si es timeout, los mensajes pueden haberse enviado
            // Registramos el error pero no marcamos definitivamente como fallidos
            if (str_contains($errorMessage, 'timeout') || str_contains($errorMessage, 'timed out')) {
                Log::warning("Timeout en chunk {$chunkNumber} - mensajes pueden haberse enviado", [
                    'campania_id' => $this->campania->id_campania,
                    'chunk_size' => count($chunk),
                    'error' => $errorMessage,
                    'nota' => 'Verificar manualmente si los mensajes se enviaron'
                ]);
                
                // Marcar como pendientes en vez de fallidos para revisión manual
                foreach ($chunk as $destinatario) {
                    WatModal::create([
                        'id_modalservicio' => $destinatario['id_modalservicio'],
                        'number_message' => 1,
                        'estado' => 0,
                        'error' => 'Timeout en envío - requiere verificación manual',
                        'fecha' => now(),
                        'campania_id' => $this->campania->id_campania, // 🔁 FASE 3
                        'intentos' => 1, // 🔁 FASE 3
                        'puede_reintentar' => true, // 🔁 FASE 3
                    ]);
                }
            } else {
                // Error real (no timeout)
                $this->campania->increment('envios_fallidos', count($chunk));
                $this->campania->decrement('envios_pendientes', count($chunk));
                
                Log::error("Error procesando chunk {$chunkNumber}", [
                    'campania_id' => $this->campania->id_campania,
                    'error' => $errorMessage
                ]);
                
                // Registrar todos como fallidos
                foreach ($chunk as $destinatario) {
                    WatModal::create([
                        'id_modalservicio' => $destinatario['id_modalservicio'],
                        'number_message' => 1,
                        'estado' => 0,
                        'error' => $errorMessage,
                        'fecha' => now(),
                        'campania_id' => $this->campania->id_campania, // 🔁 FASE 3
                        'intentos' => 1, // 🔁 FASE 3
                        'puede_reintentar' => true, // 🔁 FASE 3
                    ]);
                }
            }
        }
    }

    /**
     * 🔁 FASE 3: Procesa reintentos para mensajes fallidos (máximo 3 intentos)
     */
    private function processRetries()
    {
        $maxRetries = 3;
        
        for ($intento = 2; $intento <= $maxRetries; $intento++) {
            // Obtener mensajes fallidos del intento anterior
            $fallidosPendientes = WatModal::where('campania_id', $this->campania->id_campania)
                ->where('estado', 0)
                ->where('intentos', $intento - 1)
                ->where('puede_reintentar', true)
                ->with('modalServicio')
                ->get();

            if ($fallidosPendientes->isEmpty()) {
                Log::info("No hay mensajes para reintento #{$intento}", [
                    'campania_id' => $this->campania->id_campania
                ]);
                continue; // No hay nada que reintentar
            }

            Log::info("Iniciando reintento #{$intento}", [
                'campania_id' => $this->campania->id_campania,
                'total_a_reintentar' => $fallidosPendientes->count()
            ]);

            // Dividir en chunks de 20 para reintentos
            $retryChunks = $fallidosPendientes->chunk(20);
            
            foreach ($retryChunks as $chunkIndex => $retryChunk) {
                // Verificar límite diario
                $this->campania->refresh();
                $remainingQuota = $this->campania->getRemainingDailyQuota();
                
                if ($remainingQuota <= 0) {
                    Log::warning("Límite diario alcanzado durante reintento #{$intento}", [
                        'campania_id' => $this->campania->id_campania,
                        'envios_hoy' => $this->campania->envios_hoy
                    ]);
                    $this->campania->update(['estado' => 'pausada_hasta_mañana']);
                    return; // Detener reintentos por hoy
                }

                // Preparar destinatarios para reintento
                $recipients = $retryChunk->map(function ($watModal) {
                    $modalServicio = $watModal->modalServicio;
                    return [
                        'id_modalservicio' => $modalServicio->id_modalservicio,
                        'nombre' => $modalServicio->nombre,
                        'telefono' => formatearTelefonoWhatsApp($modalServicio->telefono),
                        'wat_modal_id' => $watModal->id_modal_wat, // Para actualizar después
                    ];
                })->toArray();

                $payload = [
                    'campania_id' => $this->campania->id_campania,
                    'chunk_number' => $chunkIndex + 1,
                    'recipients' => $recipients,
                    'message' => $this->campania->parrafo,
                    'image_url' => $this->campania->imagen_url,
                    'id_servicio' => $this->campania->id_servicio,
                    'is_retry' => true, // 🔁 Marcar como reintento
                    'retry_attempt' => $intento,
                ];

                try {
                    $response = Http::timeout(600)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'X-API-Key' => env('WHATSAPP_SERVICE_API_KEY'),
                        ])
                        ->post(env('WHATSAPP_API_URL') . '/api/whatsapp/send-campaign-batch', $payload);

                    if ($response->successful()) {
                        $result = $response->json();
                        $exitosos = $result['successful'] ?? 0;
                        $fallidos = $result['failed'] ?? 0;

                        // Actualizar contadores de campaña
                        $this->campania->increment('envios_exitosos', $exitosos);
                        $this->campania->increment('envios_hoy', $exitosos + $fallidos);
                        $this->campania->update(['fecha_ultimo_envio' => now()->toDateString()]);

                        // Actualizar registros de WatModal
                        foreach ($retryChunk as $watModal) {
                            $idModalServicio = $watModal->id_modalservicio;
                            $success = $result['results'][$idModalServicio]['success'] ?? false;

                            if ($success) {
                                // Exitoso en reintento
                                $watModal->update([
                                    'estado' => 1,
                                    'error' => null,
                                    'intentos' => $intento,
                                    'puede_reintentar' => false,
                                ]);
                            } else {
                                // Falló nuevamente
                                $watModal->update([
                                    'estado' => 0,
                                    'error' => $result['results'][$idModalServicio]['error'] ?? "Falló en intento #{$intento}",
                                    'intentos' => $intento,
                                    'puede_reintentar' => $intento < $maxRetries,
                                ]);
                            }
                        }

                        Log::info("Reintento #{$intento} - Chunk procesado", [
                            'campania_id' => $this->campania->id_campania,
                            'exitosos' => $exitosos,
                            'fallidos' => $fallidos
                        ]);

                    } else {
                        // Error en el request de reintento
                        Log::error("Reintento #{$intento} - Request falló", [
                            'campania_id' => $this->campania->id_campania,
                            'status' => $response->status()
                        ]);

                        // Incrementar intentos sin cambiar estado
                        foreach ($retryChunk as $watModal) {
                            $watModal->update([
                                'intentos' => $intento,
                                'error' => "Request falló en intento #{$intento}: HTTP " . $response->status(),
                                'puede_reintentar' => $intento < $maxRetries,
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    Log::error("Excepción en reintento #{$intento}", [
                        'campania_id' => $this->campania->id_campania,
                        'error' => $e->getMessage()
                    ]);

                    // Marcar como no reintentables si es el último intento
                    foreach ($retryChunk as $watModal) {
                        $watModal->update([
                            'intentos' => $intento,
                            'error' => "Excepción en intento #{$intento}: " . $e->getMessage(),
                            'puede_reintentar' => $intento < $maxRetries,
                        ]);
                    }
                }

                // Pausa entre chunks de reintento
                if ($chunkIndex < $retryChunks->count() - 1) {
                    sleep(120); // 2 minutos
                }
            }

            // Pausa entre intentos
            if ($intento < $maxRetries) {
                Log::info("Pausa de 5 minutos antes del siguiente reintento", [
                    'campania_id' => $this->campania->id_campania
                ]);
                sleep(300); // 5 minutos entre intentos completos
            }
        }

        Log::info('Reintentos completados', [
            'campania_id' => $this->campania->id_campania,
            'retry_stats' => $this->campania->getRetryStats()
        ]);
    }

    /**
     * Manejo de fallos del Job
     */
    public function failed(\Throwable $exception)
    {
        Log::error('SendWhatsAppCampaignJob falló definitivamente', [
            'campania_id' => $this->campania->id_campania,
            'error' => $exception->getMessage()
        ]);

        $this->campania->update([
            'estado' => 'error',
            'fecha_fin' => now()
        ]);
    }
}
