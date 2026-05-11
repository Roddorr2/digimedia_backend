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
        /*
        return [
            'id_blog' => $this->id_blog,
            'id_blog_head' => $this->id_blog_head,
            'id_blog_body' => $this->id_blog_body,
            'id_blog_footer' => $this->id_blog_footer,
            'fecha' => $this->fecha,
            'link' => $this->link,
            'card' => $this->whenLoaded('card'),
        ];
        */
        return [
            'id_blog' => $this->id_blog,

            'fecha' => $this->fecha,

            'link' => $this->link,

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

