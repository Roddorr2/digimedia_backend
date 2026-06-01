<?php

namespace App\DTOs\ConfiguracionTiempo;

use Illuminate\Http\Request;

class UpdateConfiguracionTiempoDTO
{
    public function __construct(
        public readonly array $email,
        public readonly array $whatsapp
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            email: $request->input('email', []),
            whatsapp: $request->input('whatsapp', [])
        );
    }
}
