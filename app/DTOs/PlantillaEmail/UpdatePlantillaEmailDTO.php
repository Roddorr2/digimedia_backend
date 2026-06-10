<?php

namespace App\DTOs\PlantillaEmail;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class UpdatePlantillaEmailDTO
{
    public function __construct(
        public readonly string $asunto,
        public readonly string $encabezado,
        public readonly ?string $color,
        public readonly string $mensaje,
        public readonly ?UploadedFile $imagen,
        public readonly ?string $mensaje_boton,
        public readonly ?string $url_boton,
        public readonly ?string $footer,
        public readonly ?string $red_facebook,
        public readonly ?string $red_tiktok,
        public readonly ?string $red_instagram,
        public readonly ?string $red_linkedin
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            asunto: $request->input('asunto'),
            encabezado: $request->input('encabezado'),
            color: $request->input('color'),
            mensaje: $request->input('mensaje'),
            imagen: $request->file('imagen'),
            mensaje_boton: $request->input('mensaje_boton'),
            url_boton: $request->input('url_boton'),
            footer: $request->input('footer'),
            red_facebook: $request->input('red_facebook'),
            red_tiktok: $request->input('red_tiktok'),
            red_instagram: $request->input('red_instagram'),
            red_linkedin: $request->input('red_linkedin')
        );
    }

    public function toArray(): array
    {
        return [
            'asunto'        => $this->asunto,
            'encabezado'    => $this->encabezado,
            'color'         => $this->color ?? '#8a2be2',
            'mensaje'       => $this->mensaje,
            'mensaje_boton' => $this->mensaje_boton,
            'url_boton'     => $this->url_boton,
            'footer'        => $this->footer,
            'red_facebook'  => $this->red_facebook,
            'red_tiktok'    => $this->red_tiktok,
            'red_instagram' => $this->red_instagram,
            'red_linkedin'  => $this->red_linkedin,
        ];
    }
}
