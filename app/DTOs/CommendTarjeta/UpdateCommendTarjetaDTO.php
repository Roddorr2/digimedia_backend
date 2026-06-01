<?php

namespace App\DTOs\CommendTarjeta;

use Illuminate\Http\Request;

class UpdateCommendTarjetaDTO
{
    public function __construct(
        public readonly array $data
    ) {}

    public static function fromRequest(Request $request): self
    {
        $fields = ['titulo', 'texto1', 'texto2', 'texto3', 'texto4', 'texto5'];
        $data = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }
        return new self($data);
    }
}
