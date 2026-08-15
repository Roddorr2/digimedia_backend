<?php

namespace Tests\Unit;

use App\DTOs\BlogBody\UpdateBlogBodyDTO;
use App\Http\Resources\BlogBodyResource;
use App\Models\BlogBody;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class BlogBodyDataTest extends TestCase
{
    public function test_update_dto_keeps_word_and_link_for_header_description(): void
    {
        $data = UpdateBlogBodyDTO::fromArray([
            'titulo' => 'Título',
            'descripcion' => 'Descripción con palabra enlazada',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
        ])->toArray();

        $this->assertSame('palabra', $data['palabra']);
        $this->assertSame('https://example.com/destino', $data['enlace']);
    }

    public function test_update_dto_defaults_word_and_link_to_null_when_omitted(): void
    {
        $data = UpdateBlogBodyDTO::fromArray([
            'titulo' => 'Título actualizado',
            'descripcion' => 'Descripción sin palabra enlazada',
        ])->toArray();

        $this->assertNull($data['palabra']);
        $this->assertNull($data['enlace']);
        $this->assertSame('Título actualizado', $data['titulo']);
        $this->assertSame('Descripción sin palabra enlazada', $data['descripcion']);
    }

    public function test_blog_body_resource_returns_word_and_link_at_root_level(): void
    {
        $blogBody = new BlogBody();
        $blogBody->forceFill([
            'id_blog_body' => 5,
            'titulo' => 'Título',
            'descripcion' => 'Descripción con palabra enlazada',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
        ]);

        $data = (new BlogBodyResource($blogBody))->resolve(Request::create('/blog_body/5'));

        $this->assertSame('palabra', $data['palabra']);
        $this->assertSame('https://example.com/destino', $data['enlace']);
    }
}
