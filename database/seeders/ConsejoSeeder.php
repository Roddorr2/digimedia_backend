<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsejoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consejos_por_blog_body = [
            1 => [
                "Opta por colores que reflejen la personalidad de tu bar.",
                "Elige un diseño legible y atractivo.",
                "Considera el lugar de instalación para maximizar su impacto.",
            ],
            2 => [
                "Limpia regularmente para mantener el brillo.",
                "Verifica que no haya fugas de gas.",
                "La vida útil puede alcanzar 15 años.",
            ],
            3 => [
                "Contrata electricistas certificados.",
                "Cumple con normativas locales de seguridad.",
                "Asegura la resistencia estructural del soporte.",
            ],
            4 => [
                "Iluminación RGB inteligente y controlable por app.",
                "Diseños minimalistas pero impactantes.",
                "Integración con sistemas de videomapping.",
            ],
            5 => [
                "Inversión promedio se recupera en 18 meses.",
                "Aumento de visibilidad nocturna del 300%.",
                "Reducción de consumo energético de 75-80%.",
            ],
            6 => [
                "Más de 500 negocios transformados.",
                "Incremento promedio de ventas del 35%.",
                "100% de clientes recomendarían el servicio.",
            ],
            7 => [
                "Luz cálida aumenta percepción de confort 40%.",
                "Luz dinámica mejora energía y vitalidad.",
                "Iluminación correcta reduce estrés del cliente.",
            ],
            8 => [
                "Luz matutina energizante para actitud positiva.",
                "Luz vespertina cálida para relajación.",
                "Luz nocturna versátil según actividad.",
            ],
            9 => [
                "Control remoto vía smartphone o tablet.",
                "Automatización según horarios predefinidos.",
                "Integración con sistemas de climatización.",
            ],
            10 => [
                "Ahorro de hasta 80% en factura eléctrica.",
                "Materiales eco-friendly y reciclables.",
                "Longevidad de 50,000 horas de uso.",
            ],
            11 => [
                "Cada pieza debe brillar individualmente.",
                "Luz que envuelve sin abrumar al cliente.",
                "Contraste perfecto para resaltar productos.",
            ],
            12 => [
                "Iluminación que acompaña cada plato.",
                "Luz cálida que anima la conversación.",
                "Ambientes versátiles según la hora.",
            ],
            13 => [
                "Luz sin radiación UV dañina.",
                "Precisión cromática para colores reales.",
                "Sistemas ajustables para diferentes obras.",
            ],
            14 => [
                "Pasillos que generan anticipación.",
                "Áreas de venta con luz atractiva.",
                "Transiciones suaves hacia salas oscuras.",
            ],
            15 => [
                "Luz natural simulada en horario de día.",
                "Reducción de fatiga visual certificada.",
                "Espacios de descanso con luz cálida.",
            ],
            16 => [
                "Iluminación clínica en consultorios.",
                "Confort en salas de tratamiento.",
                "Calidez en recuperación post-tratamiento.",
            ],
            17 => [
                "Presupuesto sin sacrificar calidad.",
                "Sistemas eficientes y económicos.",
                "ROI en menos de 2 años.",
            ],
            18 => [
                "Sistemas RGB dinámicos con música.",
                "Efectos que crean momentos únicos.",
                "Control remoto para cambios en vivo.",
            ],
        ];

        $consejos = [];
        foreach ($consejos_por_blog_body as $idBlogBody => $textos) {
            foreach ($textos as $orden => $texto) {
                $consejos[] = [
                    'texto' => $texto,
                    'orden' => $orden,
                    'id_blog_body' => $idBlogBody,
                ];
            }
        }

        DB::table('consejos')->insert($consejos);
    }
}
