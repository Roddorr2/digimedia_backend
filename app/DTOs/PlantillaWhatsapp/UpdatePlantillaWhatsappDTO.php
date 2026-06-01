<?php

namespace App\DTOs\PlantillaWhatsapp;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class UpdatePlantillaWhatsappDTO
{
    public function __construct(
        public readonly string $mensaje,
        public readonly ?UploadedFile $imagen
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            mensaje: $request->input('mensaje'),
            imagen: $request->file('imagen')
        );
    }
}
