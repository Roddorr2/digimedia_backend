<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CommendTarjetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commend_tarjetas = [
            [
                'titulo' => "Consejos para Elegir el Letrero Perfecto",
                'texto1' => "Opta por colores que reflejen la personalidad de tu bar.",
                'texto2' => "Elige un diseño legible y atractivo.",
                'texto3' => "Considera el lugar de instalación para maximizar su impacto.",
            ],
            [
                'titulo' => "Mantenimiento de Letrero Neón",
                'texto1' => "Limpia regularmente para mantener el brillo.",
                'texto2' => "Verifica que no haya fugas de gas.",
                'texto3' => "La vida útil puede alcanzar 15 años.",
            ],
            [
                'titulo' => "Regulaciones y Instalación",
                'texto1' => "Contrata electricistas certificados.",
                'texto2' => "Cumple con normativas locales de seguridad.",
                'texto3' => "Asegura la resistencia estructural del soporte.",
            ],
            [
                'titulo' => "Tendencias en Iluminación 2024-2025",
                'texto1' => "Iluminación RGB inteligente y controlable por app.",
                'texto2' => "Diseños minimalistas pero impactantes.",
                'texto3' => "Integración con sistemas de videomapping.",
            ],
            [
                'titulo' => "ROI en Iluminación Premium",
                'texto1' => "Inversión promedio se recupera en 18 meses.",
                'texto2' => "Aumento de visibilidad nocturna del 300%.",
                'texto3' => "Reducción de consumo energético de 75-80%.",
            ],
            [
                'titulo' => "Casos de Éxito",
                'texto1' => "Más de 500 negocios transformados.",
                'texto2' => "Incremento promedio de ventas del 35%.",
                'texto3' => "100% de clientes recomendarían el servicio.",
            ],
            [
                'titulo' => "Efectos Psicológicos de la Luz",
                'texto1' => "Luz cálida aumenta percepción de confort 40%.",
                'texto2' => "Luz dinámica mejora energía y vitalidad.",
                'texto3' => "Iluminación correcta reduce estrés del cliente.",
            ],
            [
                'titulo' => "Iluminación según Horarios",
                'texto1' => "Luz matutina energizante para actitud positiva.",
                'texto2' => "Luz vespertina cálida para relajación.",
                'texto3' => "Luz nocturna versátil según actividad.",
            ],
            [
                'titulo' => "Tecnología Inteligente en Sistemas LED",
                'texto1' => "Control remoto vía smartphone o tablet.",
                'texto2' => "Automatización según horarios predefinidos.",
                'texto3' => "Integración con sistemas de climatización.",
            ],
            [
                'titulo' => "Sostenibilidad y Eficiencia",
                'texto1' => "Ahorro de hasta 80% en factura eléctrica.",
                'texto2' => "Materiales eco-friendly y reciclables.",
                'texto3' => "Longevidad de 50,000 horas de uso.",
            ],
            // Enero 2026
            [
                'titulo' => "Boutiques: Exclusividad Iluminada",
                'texto1' => "Cada pieza debe brillar individualmente.",
                'texto2' => "Luz que envuelve sin abrumar al cliente.",
                'texto3' => "Contraste perfecto para resaltar productos.",
            ],
            [
                'titulo' => "Restaurantes Finos: Detalles Destellantes",
                'texto1' => "Iluminación que acompaña cada plato.",
                'texto2' => "Luz cálida que anima la conversación.",
                'texto3' => "Ambientes versátiles según la hora.",
            ],
            [
                'titulo' => "Museos: Preservación Luminosa",
                'texto1' => "Luz sin radiación UV dañina.",
                'texto2' => "Precisión cromática para colores reales.",
                'texto3' => "Sistemas ajustables para diferentes obras.",
            ],
            [
                'titulo' => "Cines: Expectativa Visual",
                'texto1' => "Pasillos que generan anticipación.",
                'texto2' => "Áreas de venta con luz atractiva.",
                'texto3' => "Transiciones suaves hacia salas oscuras.",
            ],
            // Febrero 2026
            [
                'titulo' => "Oficinas: Concentración y Bienestar",
                'texto1' => "Luz natural simulada en horario de día.",
                'texto2' => "Reducción de fatiga visual certificada.",
                'texto3' => "Espacios de descanso con luz cálida.",
            ],
            [
                'titulo' => "Clínicas: Trust y Seguridad",
                'texto1' => "Iluminación clínica en consultorios.",
                'texto2' => "Confort en salas de tratamiento.",
                'texto3' => "Calidez en recuperación post-tratamiento.",
            ],
            [
                'titulo' => "Hoteles Económicos: Rentabilidad",
                'texto1' => "Presupuesto sin sacrificar calidad.",
                'texto2' => "Sistemas eficientes y económicos.",
                'texto3' => "ROI en menos de 2 años.",
            ],
            [
                'titulo' => "Eventos: Magia Espectacular",
                'texto1' => "Sistemas RGB dinámicos con música.",
                'texto2' => "Efectos que crean momentos únicos.",
                'texto3' => "Control remoto para cambios en vivo.",
            ],
        ];

        DB::table('commend_tarjetas')->insert($commend_tarjetas);
    }
}
