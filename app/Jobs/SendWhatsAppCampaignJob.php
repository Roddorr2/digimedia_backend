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
    public $timeout = 300; // 5 minutos por Job

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

            // Actualizar estado de la campaña
            $this->campania->update(['estado' => 'en_proceso']);

            // Dividir destinatarios en chunks de 50
            $chunks = array_chunk($this->destinatarios, 50);
            $totalChunks = count($chunks);

            Log::info('Campaña dividida en chunks', [
                'campania_id' => $this->campania->id_campania,
                'total_chunks' => $totalChunks,
                'chunk_size' => 50
            ]);

            // Procesar cada chunk
            foreach ($chunks as $index => $chunk) {
                $chunkNumber = $index + 1;
                
                Log::info("Procesando chunk {$chunkNumber}/{$totalChunks}", [
                    'campania_id' => $this->campania->id_campania,
                    'destinatarios_en_chunk' => count($chunk)
                ]);

                $this->processChunk($chunk, $chunkNumber);

                // Pequeña pausa entre chunks para no saturar
                if ($chunkNumber < $totalChunks) {
                    sleep(2);
                }
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
            $response = Http::timeout(120) // 2 minutos timeout
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post(config('services.whatsapp.url') . '/api/send-campaign-batch', $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                // Actualizar contadores según respuesta
                $exitosos = $result['successful'] ?? 0;
                $fallidos = $result['failed'] ?? 0;

                $this->campania->increment('envios_exitosos', $exitosos);
                $this->campania->increment('envios_fallidos', $fallidos);
                $this->campania->decrement('envios_pendientes', $exitosos + $fallidos);

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
                    ]);
                }

                Log::info("Chunk {$chunkNumber} procesado", [
                    'campania_id' => $this->campania->id_campania,
                    'exitosos' => $exitosos,
                    'fallidos' => $fallidos
                ]);

            } else {
                // Si falla el request completo, marcar todos como fallidos
                $this->campania->increment('envios_fallidos', count($chunk));
                $this->campania->decrement('envios_pendientes', count($chunk));

                foreach ($chunk as $destinatario) {
                    WatModal::create([
                        'id_modalservicio' => $destinatario['id_modalservicio'],
                        'number_message' => 1,
                        'estado' => 0,
                        'error' => 'Error en request a whatsapp-service: ' . $response->status(),
                        'fecha' => now(),
                    ]);
                }

                Log::error("Chunk {$chunkNumber} falló completamente", [
                    'campania_id' => $this->campania->id_campania,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            // Manejo de errores del chunk
            $this->campania->increment('envios_fallidos', count($chunk));
            $this->campania->decrement('envios_pendientes', count($chunk));

            Log::error("Error procesando chunk {$chunkNumber}", [
                'campania_id' => $this->campania->id_campania,
                'error' => $e->getMessage()
            ]);
        }
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
