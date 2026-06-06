<?php

namespace App\Mail;

use App\Models\PlantillaEmail;
use App\Models\servicios;
use App\Models\Subservicio;
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
    public ?int $id_subservicio;

    public function __construct($number_message, $data, $id_service, ?int $id_subservicio = null)
    {
        $this->number_message  = $number_message;
        $this->data            = $data;
        $this->id_service      = $id_service;
        $this->id_subservicio  = $id_subservicio;
    }

    public function build()
    {
        $plantilla = $this->resolverPlantilla();

        if (!$plantilla) {
            Log::error("Plantilla Email no encontrada", [
                'servicio'      => $this->id_service,
                'subservicio'   => $this->id_subservicio,
                'numero'        => $this->number_message,
            ]);

            return $this->subject('Información de nuestros servicios')
                ->view('mails.modal')
                ->with([
                    'data'          => $this->data,
                    'send_message'  => 'Lo sentimos, pero no pudimos cargar el contenido del mensaje.',
                    'title'         => 'Error en el sistema',
                    'image'         => null,
                    'mensaje_boton' => 'Contactar Soporte',
                    'url_boton'     => 'https://wa.me/51983027828',
                    'footer'        => '© ' . date('Y') . ' DigiMedia Marketing. Todos los derechos reservados.',
                    'redes'         => [],
                ]);
        }

        $mensaje = str_replace('{nombre}', $this->data['nombre'], $plantilla->mensaje);

        // Solo Cloudinary o URLs absolutas llegan a Gmail — localhost no es accesible
        $imagen = $plantilla->imagen_url;
        if (!empty($imagen) && !preg_match('/^https?:\/\//i', $imagen)) {
            $imagen = null; // descarta rutas locales para no romper el layout
        }

        $redes = array_filter([
            'facebook'  => $plantilla->red_facebook,
            'tiktok'    => $plantilla->red_tiktok,
            'instagram' => $plantilla->red_instagram,
            'linkedin'  => $plantilla->red_linkedin,
        ]);

        return $this->subject($plantilla->asunto)
            ->view('mails.modal')
            ->with([
                'data'          => $this->data,
                'send_message'  => $mensaje,
                'title'         => $plantilla->encabezado,
                'image'         => $imagen,
                'mensaje_boton' => $plantilla->mensaje_boton ?? '¡CONTÁCTANOS!',
                'url_boton'     => $plantilla->url_boton ?? 'https://wa.me/51983027828',
                'footer'        => $plantilla->footer ?? '© ' . date('Y') . ' DigiMedia Marketing. Todos los derechos reservados.',
                'redes'         => $redes,
            ]);
    }

    private function resolverPlantilla(): ?PlantillaEmail
    {
        // 1. Intentar plantilla del subservicio
        if ($this->id_subservicio) {
            $plantilla = PlantillaEmail::where('plantillable_type', Subservicio::class)
                ->where('plantillable_id', $this->id_subservicio)
                ->where('numero_plantilla', $this->number_message)
                ->first();

            if ($plantilla) {
                Log::info("Usando plantilla email de subservicio {$this->id_subservicio}");
                return $plantilla;
            }

            Log::info("Sin plantilla email para subservicio {$this->id_subservicio} — usando servicio padre");
        }

        // 2. Fallback: plantilla del servicio
        return PlantillaEmail::where('plantillable_type', servicios::class)
            ->where('plantillable_id', $this->id_service)
            ->where('numero_plantilla', $this->number_message)
            ->first();
    }
}
