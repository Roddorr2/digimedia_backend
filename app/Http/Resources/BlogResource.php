<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_blog' => $this->id_blog,
            'fecha' => $this->fecha,
            'link' => $this->link,

            // 🔥 IDs planos (esto soluciona tu problema)
            'id_blog_head' => $this->head->id_blog_head ?? null,
            'id_blog_body' => $this->body->id_blog_body ?? null,
            'id_blog_footer' => $this->footer->id_blog_footer ?? null,

            // 🔥 Mantienes también los objetos completos (best of both worlds)
            'head' => new BlogHeadResource(
                $this->whenLoaded('head')
            ),

            'body' => new BlogBodyResource(
                $this->whenLoaded('body')
            ),

            'footer' => new BlogFooterResource(
                $this->whenLoaded('footer')
            ),

            'card' => new CardResource(
                $this->whenLoaded('card')
            ),

            'auditoria' => BlogAuditoriaResource::collection(
                $this->whenLoaded('blogAuditoria')
            ),
        ];
    }
}
