<?php

namespace App\DTOs\ConfiguracionTiempo;

use Illuminate\Http\Request;

class StoreConfiguracionTiempoDTO
{
    public function __construct(
        public readonly string $tipo,
        public readonly int $numero_mensaje,
        public readonly string $unidad_tiempo,
        public readonly int $valor_tiempo
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            tipo: $request->input('tipo'),
            numero_mensaje: (int) $request->input('numero_mensaje'),
            unidad_tiempo: $request->input('unidad_tiempo'),
            valor_tiempo: (int) $request->input('valor_tiempo')
        );
    }

    public function toArray(): array
    {
        return [
            'tipo' => $this->tipo,
            'numero_mensaje' => $this->numero_mensaje,
            'unidad_tiempo' => $this->unidad_tiempo,
            'valor_tiempo' => $this->valor_tiempo,
        ];
    }
}
