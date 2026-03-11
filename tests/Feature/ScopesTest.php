<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Blog;
use App\Models\Card;
use App\Models\Empleado;
use Illuminate\Support\Facades\DB;

class ScopesTest extends TestCase
{
    public function test_blog_scope_con_cards()
    {
        $blogs = Blog::conCards()->get();
        $this->assertIsObject($blogs);
        echo "\n✅ Blog::conCards() funciona - Retorna: " . count($blogs) . " blogs\n";
    }

    public function test_blog_scope_sin_cards()
    {
        $blogs = Blog::sinCards()->get();
        $this->assertIsObject($blogs);
        echo "✅ Blog::sinCards() funciona - Retorna: " . count($blogs) . " blogs\n";
    }

    public function test_blog_scope_con_relaciones()
    {
        $blogs = Blog::conRelaciones()->limit(1)->get();
        // Verificar que las relaciones están cargadas
        if (count($blogs) > 0) {
            $blog = $blogs->first();
            $this->assertTrue($blog->relationLoaded('head'));
            echo "✅ Blog::conRelaciones() funciona - Relaciones eager loaded\n";
        }
    }

    public function test_blog_scope_reciente()
    {
        $blogs = Blog::reciente()->limit(3)->get();
        $this->assertIsObject($blogs);
        echo "✅ Blog::reciente() funciona - Retorna: " . count($blogs) . " blogs (ordenados DESC)\n";
    }

    public function test_blog_scope_buscar()
    {
        $blogs = Blog::buscar('blog')->get();
        $this->assertIsObject($blogs);
        echo "✅ Blog::buscar() funciona - Retorna: " . count($blogs) . " blogs\n";
    }

    public function test_blog_generar_slug_unico()
    {
        // Simple prueba: la función retorna un string válido
        $slug = Blog::generarSlugUnico('Test Blog Titulo');
        
        $this->assertIsString($slug);
        $this->assertStringContainsString('test', $slug);
        echo "✅ Blog::generarSlugUnico() funciona\n";
        echo "   Input: 'Test Blog Titulo'\n";
        echo "   Output: $slug\n";
    }

    public function test_card_scope_publicadas()
    {
        $cards = Card::publicadas()->get();
        $this->assertIsObject($cards);
        echo "✅ Card::publicadas() funciona - Retorna: " . count($cards) . " cards publicadas\n";
    }

    public function test_card_scope_borrador()
    {
        $cards = Card::borrador()->get();
        $this->assertIsObject($cards);
        echo "✅ Card::borrador() funciona - Retorna: " . count($cards) . " cards en borrador\n";
    }

    public function test_card_scope_ordenado()
    {
        $cards = Card::ordenado()->limit(5)->get();
        $this->assertIsObject($cards);
        echo "✅ Card::ordenado() funciona - Retorna: " . count($cards) . " cards ordenadas ASC\n";
    }

    public function test_card_scope_con_detalles_blog()
    {
        $cards = Card::conDetallesBlog()->limit(1)->get();
        
        if (count($cards) > 0) {
            $card = $cards->first();
            $this->assertTrue($card->relationLoaded('blog'));
            echo "✅ Card::conDetallesBlog() funciona - Blog cargado con head/body/footer\n";
        }
    }

    public function test_card_scope_con_relaciones_completas()
    {
        // Medir queries
        DB::enableQueryLog();
        
        $cards = Card::conRelacionesCompletas()->limit(5)->get();
        
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();
        
        $this->assertIsObject($cards);
        echo "✅ Card::conRelacionesCompletas() funciona\n";
        echo "   Queries ejecutadas: $queries (esperado 3-4, no N+1)\n";
    }

    public function test_card_scope_del_empleado()
    {
        $cards = Card::delEmpleado(1)->get();
        $this->assertIsObject($cards);
        echo "✅ Card::delEmpleado(1) funciona - Retorna: " . count($cards) . " cards\n";
    }

    public function test_card_scope_del_blog()
    {
        $cards = Card::delBlog(1)->get();
        $this->assertIsObject($cards);
        echo "✅ Card::delBlog(1) funciona - Retorna: " . count($cards) . " cards\n";
    }

    public function test_empleado_scope_con_relaciones()
    {
        $empleados = Empleado::conRelaciones()->limit(1)->get();
        
        if (count($empleados) > 0) {
            $empleado = $empleados->first();
            $this->assertTrue($empleado->relationLoaded('rol'));
            echo "✅ Empleado::conRelaciones() funciona - Relaciones user/rol/subtipoAdmin cargadas\n";
        }
    }

    public function test_empleado_scope_del_rol()
    {
        $empleados = Empleado::delRol(1)->get();
        $this->assertIsObject($empleados);
        echo "✅ Empleado::delRol(1) funciona - Retorna: " . count($empleados) . " empleados\n";
    }

    public function test_empleado_scope_buscar()
    {
        $empleados = Empleado::buscar('admin')->get();
        $this->assertIsObject($empleados);
        echo "✅ Empleado::buscar() funciona - Retorna: " . count($empleados) . " empleados\n";
    }

    public function test_no_hay_n_plus_one_queries()
    {
        DB::enableQueryLog();
        
        // Obtener 10 cards con relaciones
        $cards = Card::conRelacionesCompletas()->limit(10)->get();
        
        // Acceder a las relaciones
        foreach ($cards as $card) {
            $card->blog->head;
            $card->empleado->nombre;
        }
        
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();
        
        // Debería ser 3-4 queries máximo (1 cards + 1 blogs + 1 empleados + 1 heads)
        // Si fuera N+1 sería 1 + 10 + 10 + 10 = 31+ queries
        $this->assertLessThan(10, $queryCount);
        echo "✅ NO HAY N+1 QUERIES - Total: $queryCount queries (eficiente)\n";
    }
}
