<?php

namespace Database\Seeders;

use App\Models\PlantillaWhatsapp;
use App\Models\servicios;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillasWhatsappSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->buildAll() as $plantilla) {
            PlantillaWhatsapp::updateOrCreate(
                [
                    'plantillable_type' => $plantilla['plantillable_type'],
                    'plantillable_id' => $plantilla['plantillable_id'],
                    'numero_plantilla' => $plantilla['numero_plantilla'],
                ],
                $plantilla
            );
        }
    }

    private function buildAll(): array
    {
        $all = [];
        for ($servicio = 1; $servicio <= 4; $servicio++) {
            for ($num = 1; $num <= 3; $num++) {
                $all[] = $this->build($servicio, $num);
            }
        }
        return $all;
    }

    private function build(int $s, int $n): array
    {
        $data = $this->getData($s, $n);
        return array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getData(int $s, int $n): array
    {
        $textos = $this->getTextos();
        $imgs = [
            [1 => 'desarrollo_web/1-1-v2.png', 2 => 'desarrollo_web/1-2-v2.png', 3 => 'desarrollo_web/1-3-v2.png'],
            [1 => 'gestion_redes/2-1-v2.png', 2 => 'gestion_redes/2-2-v2.png', 3 => 'gestion_redes/2-3-v2.png'],
            [1 => 'marketing_digital/3-1-v2.png', 2 => 'marketing_digital/3-2-v2.png', 3 => 'marketing_digital/3-3-v2.png'],
            [1 => 'branding_diseno/4-1-v2.png', 2 => 'branding_diseno/4-2-v2.png', 3 => 'branding_diseno/4-3-v2.png'],
        ];
        $nombres = [1 => 'Diseño Web', 2 => 'Redes Sociales', 3 => 'Marketing Digital', 4 => 'Branding'];

        return [
            'plantillable_type' => servicios::class,
            'plantillable_id' => $s,
            'numero_plantilla' => $n,
            'nombre' => $nombres[$s] . " - Plantilla {$n}",
            'mensaje' => $textos["{$s}-{$n}"],
            'imagen_url' => 'imagenes/' . $imgs[$s - 1][$n],
        ];
    }

    private function getTextos(): array
    {
        return [
            '1-1' => "Hola {nombre} 👋.\n\nGracias por contactarnos. Soy de DIGIMEDIA 🚀\nA continuación, te contamos los principales beneficios que obtendrás con este servicio 👇\n\n✅ Tendrás una web profesional que genere confianza desde el primer contacto.\n\n✅ Atraerás más clientes con una experiencia clara y fácil de usar.\n\n✅ Te encontrarán en Google y llegarás a más personas interesadas.\n\n*Escríbenos y comencemos a trabajar en tu web 🚀*\n\n",
            '1-2' => "Hola {nombre} 👋.\n\nEn *DIGIMEDIA* diseñamos y desarrollamos sitios web pensados para generar confianza, atraer clientes y apoyar el crecimiento de tu negocio. 🚀\n\n*👉 Escríbenos y te ayudamos con tu desarrollo web*\n\n",
            '1-3' => "\nEn *DIGIMEDIA* trabajamos tu web para que se vea profesional, genere confianza y apoye el crecimiento de tu negocio 🚀\n \n✅ Web profesional que transmite confianza desde el primer contacto.\n\n✅ Experiencia clara y fácil de usar que atraiga más clientes.\n\n✅ Mayor visibilidad en Google para llegar a más personas interesadas.\n\n✅ Una web pensada para convertir visitas en oportunidades reales.\n\n*👉 Escríbenos para más información*\n",
            '2-1' => "Hola {nombre} 👋.\n\nGracias por contactarnos. Soy de DIGIMEDIA 🚀\n\n✅ Tendrás redes sociales profesionales alineadas a tu marca.\n\n✅ Atraerás más clientes con contenido estratégico y atractivo.\n\n\n✅ Conectarás mejor con tu audiencia y fortalecerás tu presencia digital.\n\n*Escríbenos y comencemos a trabajar tus redes sociales 🚀*\n\n",
            '2-2' => "Hola {nombre} 👋.\n\n En *DIGIMEDIA* ayudamos a marcas como la tuya a usar las redes sociales para *atraer clientes y crecer. 🚀*\n👉 *Escríbenos y te ayudamos con tus redes sociales*",
            '2-3' => "Hola {nombre} 😊.\n\nEn *DIGIMEDIA* trabajamos tus redes sociales con enfoque estratégico para que generen resultados reales 🚀\n \n👉 *Escríbenos para más información*\n",
            '3-1' => "Hola {nombre} 👋.\n\nGracias por contactarnos. Soy de DIGIMEDIA 🚀\nA continuación, te contamos los principales beneficios que obtendrás con este servicio 👇\n\n✅ Tendrás una estrategia digital clara enfocada en resultados.\n\n✅ Atraerás clientes ideales con acciones bien planificadas.\n\n✅ Tomarás mejores decisiones usando datos y métricas reales.\n\n*Escríbenos y comencemos a impulsar tu crecimiento digital 🚀*\n ",
            '3-2' => "Hola {nombre} 👋.\n\nEn *DIGIMEDIA* definimos y gestionamos estrategias digitales para que tus acciones tengan dirección, generen clientes y se basen en datos reales. 🚀\n\n👉 *Escríbenos y te ayudamos con tu conexión digital*\n",
            '3-3' => "Hola {nombre} 👋.\n\nEn *DIGIMEDIA* trabajamos tu marketing digital con estrategia y datos para lograr crecimiento real 🚀\n\n ✅ Estrategia digital clara enfocada en resultados.\n\n ✅ Acciones bien planificadas para atraer clientes ideales.\n\n ✅ Decisiones basadas en datos y métricas reales.\n\n ✅ Dirección y orden para que tu marketing sí funcione.\n\n👉 *Escríbenos para más información*\n",
            '4-1' => "Hola {nombre} 👋.\n\nGracias por contactarnos. Soy de DIGIMEDIA 🚀\n\nA continuación, te contamos los principales beneficios que obtendrás con este servicio 👇\n\n✅ Tendrás una identidad de marca clara y bien definida, que genere confianza al vender.\n\n✅ Harás crecer tu marca con una estrategia pensada para atraer y convertir.\n\n✅ Verás resultados reales gracias a una imagen potente y métricas relevantes.\n\n*Escríbenos y comencemos a construir una marca sólida 🚀*\n\n",
            '4-2' => " Hola {nombre} 👋.\n\nEn *DIGIMEDIA* trabajamos la identidad de tu marca para que se vea profesional, comunique con claridad y genere confianza desde el primer contacto. 🚀\n\n*👉 Escríbenos y te ayudamos con tu marca*\n",
            '4-3' => "\nEn *DIGIMEDIA* trabajamos tu marca para que se vea profesional, comunique con claridad y genere confianza real 🚀\n\n✅ Identidad de marca clara y bien definida.\n\n✅ Estrategia pensada para atraer, convertir y crecer.\n\n✅ Imagen profesional que genera confianza al vender.\n\n✅ Resultados medibles con una marca coherente y sólida.\n\n👉 *Escríbenos para más información*\n",
        ];
    }
}
