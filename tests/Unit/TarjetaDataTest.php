<?php

namespace Tests\Unit;

use App\DTOs\Tarjeta\UpdateTarjetaDTO;
use App\Http\Resources\BlogBodyResource;
use App\Models\BlogBody;
use App\Models\Tarjeta;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class TarjetaDataTest extends TestCase
{
    public function test_update_dto_keeps_word_and_link(): void
    {
        $request = Request::create('/tarjeta/10', 'PUT', [
            'titulo' => 'Título',
            'descripcion' => 'Descripción con palabra enlazada',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
            'id_blog_body' => 5,
        ]);

        $data = UpdateTarjetaDTO::fromRequest($request)->toArray();

        $this->assertSame('palabra', $data['palabra']);
        $this->assertSame('https://example.com/destino', $data['enlace']);
    }

    public function test_update_dto_defaults_word_and_link_to_null_when_omitted(): void
    {
        $request = Request::create('/tarjeta/10', 'PUT', [
            'titulo' => 'Título actualizado',
            'descripcion' => 'Descripción sin palabra enlazada',
            'id_blog_body' => 5,
        ]);

        $data = UpdateTarjetaDTO::fromRequest($request)->toArray();

        $this->assertNull($data['palabra']);
        $this->assertNull($data['enlace']);
        $this->assertSame('Título actualizado', $data['titulo']);
        $this->assertSame('Descripción sin palabra enlazada', $data['descripcion']);
        $this->assertSame(5, $data['id_blog_body']);
    }

    public function test_blog_body_resource_returns_word_and_link_for_each_card(): void
    {
        $body = new BlogBody();
        $body->forceFill(['id_blog_body' => 5]);

        $tarjeta = new Tarjeta();
        $tarjeta->forceFill([
            'id_tarjeta' => 10,
            'titulo' => 'Título',
            'descripcion' => 'Descripción con palabra enlazada',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
            'id_blog_body' => 5,
        ]);

        $body->setRelation('tarjetas', collect([$tarjeta]));

        $data = (new BlogBodyResource($body))->resolve(Request::create('/blog_body/5'));

        $this->assertSame('palabra', $data['tarjetas'][0]['palabra']);
        $this->assertSame('https://example.com/destino', $data['tarjetas'][0]['enlace']);
    }
}
