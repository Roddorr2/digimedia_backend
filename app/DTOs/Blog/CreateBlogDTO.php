<?php

namespace App\DTOs\Blog;

use App\Http\Requests\Blog\StoreBlogRequest;

class CreateBlogDTO
{
    public function __construct(
        public readonly int $id_blog_head,
        public readonly int $id_blog_body,
        public readonly int $id_blog_footer,
        public readonly string $fecha,
        public readonly string $link
    ) {}

    public static function fromRequest(StoreBlogRequest $request, string $generatedLink): self
    {
        $data = $request->validated();
        return new self(
            id_blog_head: $data['id_blog_head'],
            id_blog_body: $data['id_blog_body'],
            id_blog_footer: $data['id_blog_footer'],
            fecha: $data['fecha'],
            link: $generatedLink
        );
    }

    public function toArray(): array
    {
        return [
            'id_blog_head' => $this->id_blog_head,
            'id_blog_body' => $this->id_blog_body,
            'id_blog_footer' => $this->id_blog_footer,
            'fecha' => $this->fecha,
            'link' => $this->link,
        ];
    }
}