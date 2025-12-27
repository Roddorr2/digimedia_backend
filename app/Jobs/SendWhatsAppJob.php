<?php

namespace App\Jobs;

use App\Models\WatModal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $watModal;
    public $data;
    public $id_servicio;

    public function __construct(WatModal $watModal, array $data, int $id_servicio)
    {
        $this->watModal = $watModal;
        $this->data = $data;
        $this->id_servicio = $id_servicio;
    }

    public function handle()
    {
        try {
            if (!$this->watModal) {
                Log::error('WatModal no encontrado');
                return;
            }

            Log::info('Enviando WhatsApp', [
                'url' => config('services.whatsapp.url') . '/api/send-message',
                'payload' => [
                    'telefono' => '51' . $this->data['telefono'],
                    'nombre' => $this->data['nombre'],
                    'templateOption' => (int) $this->watModal->number_message,
                    'id_servicio' => (int) $this->id_servicio,
                ]
            ]);


            $response = Http::post(config('services.whatsapp.url') . '/api/send-message', [
                'telefono' => '51' . $this->data['telefono'],
                'nombre' => $this->data['nombre'],
                'templateOption' => (int) $this->watModal->number_message,
                'id_servicio' => (int) $this->id_servicio,
            ]);

            Log::info('Respuesta WhatsApp', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);


            if ($response->failed()) {
                throw new \Exception($response->body());
            }

            $this->watModal->update([
                'estado' => 1,
                'fecha' => now(),
            ]);

        } catch (\Exception $e) {
            $this->watModal->update([
                'estado' => 1,
                'error' => $e->getMessage(),
                'fecha' => now(),
            ]);

            Log::error('Error WhatsApp', [
                'id_modal_wat' => $this->watModal->id_modal_wat,
                'error' => $e->getMessage()
            ]);
        }
    }
}
