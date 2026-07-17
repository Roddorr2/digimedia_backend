<?php

namespace Tests\Unit;

use App\DTOs\CommendTarjeta\CreateCommendTarjetaDTO;
use App\DTOs\CommendTarjeta\UpdateCommendTarjetaDTO;
use App\Http\Resources\BlogBodyResource;
use App\Http\Resources\CommendTarjetaResource;
use App\Models\BlogBody;
use App\Models\CommendTarjeta;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class CommendTarjetaDataTest extends TestCase
{
    public function test_create_dto_keeps_word_and_link(): void
    {
        $request = Request::create('/commend_tarjeta', 'POST', [
            'titulo' => 'Consejos para Elegir el Letrero Perfecto',
            'texto1' => 'Texto uno',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
        ]);

        $data = CreateCommendTarjetaDTO::fromRequest($request)->toArray();

        $this->assertSame('palabra', $data['palabra']);
        $this->assertSame('https://example.com/destino', $data['enlace']);
    }

    public function test_create_dto_defaults_word_and_link_to_null_when_omitted(): void
    {
        $request = Request::create('/commend_tarjeta', 'POST', [
            'titulo' => 'Consejos para Elegir el Letrero Perfecto',
            'texto1' => 'Texto uno',
        ]);

        $data = CreateCommendTarjetaDTO::fromRequest($request)->toArray();

        $this->assertNull($data['palabra']);
        $this->assertNull($data['enlace']);
        $this->assertSame('Texto uno', $data['texto1']);
    }

    public function test_update_dto_only_includes_word_and_link_when_present(): void
    {
        $request = Request::create('/commend_tarjeta/1', 'PUT', [
            'titulo' => 'Título actualizado',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
        ]);

        $dto = UpdateCommendTarjetaDTO::fromRequest($request);

        $this->assertSame('palabra', $dto->data['palabra']);
        $this->assertSame('https://example.com/destino', $dto->data['enlace']);
        $this->assertSame('Título actualizado', $dto->data['titulo']);

        $requestWithoutLink = Request::create('/commend_tarjeta/1', 'PUT', [
            'titulo' => 'Solo título',
        ]);

        $dtoWithoutLink = UpdateCommendTarjetaDTO::fromRequest($requestWithoutLink);

        $this->assertArrayNotHasKey('palabra', $dtoWithoutLink->data);
        $this->assertArrayNotHasKey('enlace', $dtoWithoutLink->data);
        $this->assertSame('Solo título', $dtoWithoutLink->data['titulo']);
    }

    public function test_commend_tarjeta_resource_returns_word_and_link(): void
    {
        $commendTarjeta = new CommendTarjeta();
        $commendTarjeta->forceFill([
            'id_commend_tarjeta' => 1,
            'titulo' => 'Consejos para Elegir el Letrero Perfecto',
            'texto1' => 'Texto uno',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
        ]);

        $data = (new CommendTarjetaResource($commendTarjeta))->resolve(Request::create('/commend_tarjeta/1'));

        $this->assertSame('palabra', $data['palabra']);
        $this->assertSame('https://example.com/destino', $data['enlace']);
    }

    public function test_blog_body_resource_embeds_word_and_link_for_commend_tarjeta(): void
    {
        $commendTarjeta = new CommendTarjeta();
        $commendTarjeta->forceFill([
            'id_commend_tarjeta' => 1,
            'titulo' => 'Consejos para Elegir el Letrero Perfecto',
            'texto1' => 'Texto uno',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
        ]);

        $blogBody = new BlogBody();
        $blogBody->forceFill(['id_blog_body' => 5]);
        $blogBody->setRelation('commend_tarjeta', $commendTarjeta);

        $data = (new BlogBodyResource($blogBody))->resolve(Request::create('/blog_body/5'));

        $this->assertSame('palabra', $data['commend_tarjeta']['palabra']);
        $this->assertSame('https://example.com/destino', $data['commend_tarjeta']['enlace']);
    }
}
