<?php

namespace App\DTOs\Reclamacion;

use Illuminate\Http\Request;

class UpdateReclamacionDTO
{
    public function __construct(
        public readonly string $estado
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            estado: $request->input('estado')
        );
    }

    public function toArray(): array
    {
        return [
            'estadoReclamo' => $this->estado
        ];
    }
}
