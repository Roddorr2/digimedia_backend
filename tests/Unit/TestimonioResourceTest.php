<?php

namespace Tests\Unit;

use App\Http\Resources\TestimonioResource;
use App\Models\Testimonio;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class TestimonioResourceTest extends TestCase
{
    public function test_resource_exposes_same_fields_shown_in_nosotros(): void
    {
        $testimonio = new Testimonio();
        $testimonio->forceFill([
            'id_testimonio' => 1,
            'nombre' => 'Ana Pérez',
            'cargo' => 'Cliente frecuente',
            'texto' => 'Excelente atención.',
            'rating' => 5,
            'activo' => true,
            'fecha_testimonio' => '2026-07-20',
            'imagen_url' => 'https://cdn.example.com/ana.jpg',
        ]);

        $data = (new TestimonioResource($testimonio))->resolve(Request::create('/testimonios/home'));
        $raw = $testimonio->toArray();

        // Debe coincidir con los mismos valores que ya expone el endpoint de Nosotros (indexPublic)
        $this->assertSame($raw['nombre'], $data['nombre']);
        $this->assertSame($raw['texto'], $data['texto']);
        $this->assertSame($raw['rating'], $data['rating']);
        $this->assertSame($raw['imagen_url'], $data['imagen_url']);
        $this->assertSame($raw['fecha_testimonio'], $data['fecha_testimonio']);
        $this->assertSame($raw['cargo'], $data['cargo']);
        $this->assertSame($raw['id_testimonio'], $data['id_testimonio']);
    }
}
