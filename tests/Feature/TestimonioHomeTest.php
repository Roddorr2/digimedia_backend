<?php

namespace Tests\Feature;

use App\Models\Testimonio;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TestimonioHomeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fuerza sqlite en memoria para este test puntual, sin depender de phpunit.xml
        // ni del DB_CONNECTION del .env de quien lo corra — así nunca toca una base real.
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        // Se crea solo la tabla testimonios (en vez de correr todas las migraciones del
        // proyecto) para mantener este test aislado de otras secciones no relacionadas.
        Schema::dropIfExists('testimonios');
        Schema::create('testimonios', function (Blueprint $table) {
            $table->id('id_testimonio');
            $table->string('nombre');
            $table->string('cargo')->nullable();
            $table->text('texto');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('activo')->default(true);
            $table->date('fecha_testimonio')->nullable();
            $table->string('imagen_public_id')->nullable();
            $table->string('imagen_url')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('testimonios');
        parent::tearDown();
    }

    private function crearTestimonio(array $overrides = []): Testimonio
    {
        return Testimonio::create(array_merge([
            'nombre' => 'Cliente ' . uniqid(),
            'cargo' => 'Cliente',
            'texto' => 'Muy buen servicio.',
            'rating' => 5,
            'activo' => true,
            'fecha_testimonio' => now()->toDateString(),
        ], $overrides));
    }

    public function test_endpoint_inicio_solo_devuelve_testimonios_activos(): void
    {
        $this->crearTestimonio(['nombre' => 'Activo', 'activo' => true]);
        $this->crearTestimonio(['nombre' => 'Inactivo', 'activo' => false]);

        $response = $this->getJson('/api/testimonios/home');

        $response->assertOk();
        $nombres = collect($response->json('data'))->pluck('nombre');

        $this->assertTrue($nombres->contains('Activo'));
        $this->assertFalse($nombres->contains('Inactivo'));
    }

    public function test_endpoint_inicio_ordena_por_fecha_descendente(): void
    {
        $this->crearTestimonio(['nombre' => 'Viejo']);
        sleep(1);
        $this->crearTestimonio(['nombre' => 'Nuevo']);

        $response = $this->getJson('/api/testimonios/home');

        $nombres = collect($response->json('data'))->pluck('nombre')->values();

        $this->assertSame('Nuevo', $nombres->first());
    }

    public function test_endpoint_inicio_pagina_con_limite_configurable(): void
    {
        for ($i = 0; $i < 8; $i++) {
            $this->crearTestimonio(['nombre' => "Cliente {$i}"]);
        }

        $response = $this->getJson('/api/testimonios/home?limit=3');

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
        $this->assertSame(3, $response->json('meta.per_page'));
        $this->assertSame(8, $response->json('meta.total'));
        $this->assertSame(3, $response->json('meta.last_page'));
    }

    public function test_endpoint_inicio_devuelve_la_misma_estructura_de_datos_que_nosotros(): void
    {
        $this->crearTestimonio([
            'nombre' => 'Ana Pérez',
            'cargo' => 'Cliente frecuente',
            'texto' => 'Excelente atención.',
            'rating' => 4,
            'imagen_url' => 'https://cdn.example.com/ana.jpg',
        ]);

        $nosotros = $this->getJson('/api/testimonios')->json();
        $inicio = $this->getJson('/api/testimonios/home')->json('data');

        $itemNosotros = collect($nosotros)->firstWhere('nombre', 'Ana Pérez');
        $itemInicio = collect($inicio)->firstWhere('nombre', 'Ana Pérez');

        $this->assertNotNull($itemNosotros);
        $this->assertNotNull($itemInicio);

        foreach (['nombre', 'cargo', 'texto', 'rating', 'fecha_testimonio', 'imagen_url'] as $campo) {
            $this->assertSame(
                $itemNosotros[$campo],
                $itemInicio[$campo],
                "El campo '{$campo}' debe coincidir entre Nosotros e Inicio"
            );
        }
    }
}
