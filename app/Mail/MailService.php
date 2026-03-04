<?php

namespace App\Mail;

use App\Models\PlantillaEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MailService extends Mailable
{
    use Queueable, SerializesModels;
    
    public int $number_message;
    public $data;
    public $id_service;

    public function __construct($number_message, $data, $id_service)
    {
        $this->number_message = $number_message;
        $this->data = $data;
        $this->id_service = $id_service;
    }

    public function build()
    {
        // Obtener plantilla desde base de datos
        $plantilla = PlantillaEmail::where('id_servicio', $this->id_service)
            ->where('numero_plantilla', $this->number_message)
            ->first();

        if (!$plantilla) {
            Log::error("Plantilla Email no encontrada", [
                'servicio' => $this->id_service,
                'numero' => $this->number_message
            ]);

            // Usar plantilla de error por defecto
            return $this->subject('Error: Plantilla no encontrada')
                ->view('mails.modal')
                ->with([
                    'data' => $this->data,
                    'send_message' => 'Lo sentimos, pero no pudimos cargar el contenido del mensaje.',
                    'title' => 'Error en el sistema',
                    'image' => null,
                    'mensaje_boton' => 'Contactar Soporte',
                    'url_boton' => 'https://wa.me/51983027828',
                    'footer' => '© ' . date('Y') . ' DigiMedia Marketing. Todos los derechos reservados.',
                    'redes' => []
                ]);
        }

        // Procesar mensaje reemplazando {nombre}
        $mensaje = str_replace('{nombre}', $this->data['nombre'], $plantilla->mensaje);

        // Preparar redes sociales (solo las que tienen URL)
        $redes = [];
        if ($plantilla->red_facebook) {
            $redes['facebook'] = $plantilla->red_facebook;
        }
        if ($plantilla->red_tiktok) {
            $redes['tiktok'] = $plantilla->red_tiktok;
        }
        if ($plantilla->red_instagram) {
            $redes['instagram'] = $plantilla->red_instagram;
        }
        if ($plantilla->red_linkedin) {
            $redes['linkedin'] = $plantilla->red_linkedin;
        }

        return $this->subject($plantilla->asunto)
            ->view('mails.modal')
            ->with([
                'data' => $this->data,
                'send_message' => $mensaje,
                'title' => $plantilla->encabezado,
                'image' => $plantilla->imagen_url,
                'mensaje_boton' => $plantilla->mensaje_boton ?? '¡CONTÁCTANOS!',
                'url_boton' => $plantilla->url_boton ?? 'https://wa.me/51983027828?text=Hola%2C%20me%20gustar%C3%ADa%20obtener%20m%C3%A1s%20informaci%C3%B3n%20sobre%20sus%20servicios.',
                'footer' => $plantilla->footer ?? '© ' . date('Y') . ' DigiMedia Marketing. Todos los derechos reservados.',
                'redes' => $redes
            ]);
    }
}
