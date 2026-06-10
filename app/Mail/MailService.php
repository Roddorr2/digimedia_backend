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

        // Normalizar la URL de la imagen para que sea accesible y no apunte a localhost en producción
        $imagen = $this->normalizarImagenUrl($plantilla->imagen_url);

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
                'color'         => $plantilla->color ?? '#8a2be2',
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

    /**
     * Normaliza la URL de la imagen reemplazando hosts locales (localhost/127.0.0.1)
     * por el APP_URL configurado si este último no es local, o anteponiéndolo a rutas relativas.
     */
    private function normalizarImagenUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Si es una ruta relativa (por ejemplo, "assets/images/..."), anteponer el APP_URL
        if (!preg_match('/^https?:\/\//i', $url)) {
            return rtrim(config('app.url'), '/') . '/' . ltrim($url, '/');
        }

        // Si contiene localhost o 127.0.0.1, y el config('app.url') no es localhost, reemplazarlo
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        
        if ($host === 'localhost' || $host === '127.0.0.1') {
            $appUrl = config('app.url');
            $parsedApp = parse_url($appUrl);
            $appHost = $parsedApp['host'] ?? '';
            
            if ($appHost && $appHost !== 'localhost' && $appHost !== '127.0.0.1') {
                $scheme = $parsedApp['scheme'] ?? 'http';
                $port = isset($parsedApp['port']) ? ':' . $parsedApp['port'] : '';
                $path = $parsedUrl['path'] ?? '';
                $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
                
                return "{$scheme}://{$appHost}{$port}{$path}{$query}";
            }
        }

        return $url;
    }
}
