<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsejoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_consejo' => $this->id_consejo,
            'texto' => $this->texto,
            'enlace' => $this->enlace,
            'palabra' => $this->palabra,
            'orden' => $this->orden,
            'id_blog_body' => $this->id_blog_body,
        ];
    }
}
