<?php

namespace App\DTOs\Blog;

use App\Http\Requests\Blog\UpdateBlogRequest;

class UpdateBlogDTO
{
    public function __construct(
        public readonly int $id_blog_head,
        public readonly int $id_blog_body,
        public readonly int $id_blog_footer,
        public readonly int $id_card,
        public readonly int $id_empleado,
        public readonly string $link,
        public readonly ?string $descripcion
    ) {}

    public static function fromRequest(UpdateBlogRequest $request, string $generatedLink): self
    {
        $data = $request->validated();
        return new self(
            id_blog_head: $data['id_blog_head'],
            id_blog_body: $data['id_blog_body'],
            id_blog_footer: $data['id_blog_footer'],
            id_card: $data['id_card'],
            id_empleado: $data['id_empleado'],
            link: $generatedLink,
            descripcion: $data['descripcion'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id_blog_head' => $this->id_blog_head,
            'id_blog_body' => $this->id_blog_body,
            'id_blog_footer' => $this->id_blog_footer,
            'id_card' => $this->id_card,
            'id_empleado' => $this->id_empleado,
            'link' => $this->link,
        ];
    }
}