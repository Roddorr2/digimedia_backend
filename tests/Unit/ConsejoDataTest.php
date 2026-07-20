<?php

namespace Tests\Unit;

use App\DTOs\Consejo\CreateConsejoDTO;
use App\DTOs\Consejo\UpdateConsejoDTO;
use App\Http\Resources\BlogBodyResource;
use App\Http\Resources\ConsejoResource;
use App\Models\BlogBody;
use App\Models\Consejo;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ConsejoDataTest extends TestCase
{
    public function test_create_dto_keeps_word_and_link(): void
    {
        $request = Request::create('/consejo', 'POST', [
            'texto' => 'Opta por colores que reflejen la personalidad de tu bar.',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
            'id_blog_body' => 1,
        ]);

        $data = CreateConsejoDTO::fromRequest($request)->toArray();

        $this->assertSame('palabra', $data['palabra']);
        $this->assertSame('https://example.com/destino', $data['enlace']);
        $this->assertSame('Opta por colores que reflejen la personalidad de tu bar.', $data['texto']);
    }

    public function test_create_dto_defaults_word_and_link_to_null_when_omitted(): void
    {
        $request = Request::create('/consejo', 'POST', [
            'texto' => 'Texto uno',
            'id_blog_body' => 1,
        ]);

        $data = CreateConsejoDTO::fromRequest($request)->toArray();

        $this->assertNull($data['palabra']);
        $this->assertNull($data['enlace']);
        $this->assertSame('Texto uno', $data['texto']);
    }

    public function test_update_dto_keeps_word_and_link(): void
    {
        $request = Request::create('/consejo/1', 'PUT', [
            'texto' => 'Texto actualizado',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
            'id_blog_body' => 1,
        ]);

        $dto = UpdateConsejoDTO::fromRequest($request);

        $this->assertSame('palabra', $dto->palabra);
        $this->assertSame('https://example.com/destino', $dto->enlace);
        $this->assertSame('Texto actualizado', $dto->texto);
    }

    public function test_consejo_resource_returns_word_and_link(): void
    {
        $consejo = new Consejo();
        $consejo->forceFill([
            'id_consejo' => 1,
            'texto' => 'Texto uno',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
            'orden' => 0,
            'id_blog_body' => 1,
        ]);

        $data = (new ConsejoResource($consejo))->resolve(Request::create('/consejo/1'));

        $this->assertSame('palabra', $data['palabra']);
        $this->assertSame('https://example.com/destino', $data['enlace']);
    }

    public function test_blog_body_resource_embeds_word_and_link_for_each_consejo(): void
    {
        $consejo = new Consejo();
        $consejo->forceFill([
            'id_consejo' => 1,
            'texto' => 'Texto uno',
            'palabra' => 'palabra',
            'enlace' => 'https://example.com/destino',
            'orden' => 0,
            'id_blog_body' => 5,
        ]);

        $blogBody = new BlogBody();
        $blogBody->forceFill(['id_blog_body' => 5]);
        $blogBody->setRelation('consejos', collect([$consejo]));

        $data = (new BlogBodyResource($blogBody))->resolve(Request::create('/blog_body/5'));

        $this->assertSame('palabra', $data['consejos'][0]['palabra']);
        $this->assertSame('https://example.com/destino', $data['consejos'][0]['enlace']);
    }
}
