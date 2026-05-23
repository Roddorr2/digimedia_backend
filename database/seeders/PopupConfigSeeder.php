<?php

namespace Database\Seeders;

use App\Models\PopupConfig;
use App\Models\Servicio;
use App\Models\servicios;
use App\Models\Subservicio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PopupConfigSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ============================================================
        // POP-UPS PARA SERVICIOS (independientes)
        // ============================================================
        
        $serviciosConfigs = [
            // Servicio 1: Diseño Web y Desarrollo Web
            [
                'servicio_id' => 1,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#A855F7',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Transformamos tu negocio con diseño web profesional',
                'left_alt' => 'Diseño web para empresas',
                'right_alt' => 'Desarrollo web moderno',
                'mobile_alt' => 'Diseño web responsive',
            ],
            // Servicio 2: Gestión de Redes Sociales
            [
                'servicio_id' => 2,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#EC4899',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Conectamos tu marca con tu audiencia en redes sociales',
                'left_alt' => 'Gestión de redes sociales',
                'right_alt' => 'Community management profesional',
                'mobile_alt' => 'Redes sociales mobile',
            ],
            // Servicio 3: Marketing y Gestión Digital
            [
                'servicio_id' => 3,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#F59E0B',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Potenciamos tu negocio con estrategias digitales efectivas',
                'left_alt' => 'Marketing digital estratégico',
                'right_alt' => 'Campañas de marketing',
                'mobile_alt' => 'Marketing mobile',
            ],
            // Servicio 4: Branding y Diseño
            [
                'servicio_id' => 4,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#06B6D4',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Construimos marcas memorables que conectan con tu audiencia',
                'left_alt' => 'Branding y diseño',
                'right_alt' => 'Identidad corporativa',
                'mobile_alt' => 'Branding mobile',
            ],
        ];

        // Insertar pop-ups para servicios
        foreach ($serviciosConfigs as $config) {
            $servicioId = $config['servicio_id'];
            unset($config['servicio_id']);
            
            $servicio = servicios::find($servicioId);
            
            if ($servicio) {
                PopupConfig::updateOrCreate(
                    [
                        'popupable_type' => servicios::class,
                        'popupable_id' => $servicioId
                    ],
                    $config
                );
                
                $this->command->info("Pop-up configurado para SERVICIO ID: {$servicioId}");
            } else {
                $this->command->warn("Servicio ID {$servicioId} no encontrado");
            }
        }

        // ============================================================
        // POP-UPS PARA SUBSERVICIOS (independientes)
        // ============================================================
        
        $subserviciosConfigs = [
            // Servicio 1: Diseño Web y Desarrollo Web
            [
                'subservicio_id' => 1,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#ff70ec',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Diseñamos experiencias digitales que tus usuarios amarán',
                'left_alt' => 'Experiencia de usuario y diseño UX UI',
                'right_alt' => 'Diseño de interfaces modernas',
                'mobile_alt' => 'Diseño UX UI para móviles',
            ],
            [
                'subservicio_id' => 2,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#06B6D4',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Aparecé primero en Google y atraé más clientes',
                'left_alt' => 'Optimización SEO para buscadores',
                'right_alt' => 'Posicionamiento web en Google',
                'mobile_alt' => 'SEO para dispositivos móviles',
            ],
            [
                'subservicio_id' => 3,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#F59E0B',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Tu web perfecta en todos los dispositivos',
                'left_alt' => 'Desarrollo web responsive',
                'right_alt' => 'Sitios web adaptados a móvil y tablet',
                'mobile_alt' => 'Diseño responsive',
            ],
            [
                'subservicio_id' => 4,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#EC4899',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Conectá tu web con las herramientas que usás',
                'left_alt' => 'Integraciones digitales y APIs',
                'right_alt' => 'Conexión con herramientas externas',
                'mobile_alt' => 'Integraciones móviles',
            ],

            // Servicio 2: Gestión de Redes Sociales
            [
                'subservicio_id' => 5,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#10B981',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Contenido que engancha, inspira y convierte',
                'left_alt' => 'Estrategia de contenido para redes',
                'right_alt' => 'Planificación de contenidos digitales',
                'mobile_alt' => 'Contenido para móviles',
            ],
            [
                'subservicio_id' => 6,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#F97316',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Segmentá, impactá y convertí con anuncios inteligentes',
                'left_alt' => 'Social Ads y publicidad en redes',
                'right_alt' => 'Campañas en Facebook e Instagram Ads',
                'mobile_alt' => 'Anuncios para móviles',
            ],
            [
                'subservicio_id' => 7,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#EF4444',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Videos y reels que paran el scroll',
                'left_alt' => 'Producción audiovisual para redes',
                'right_alt' => 'Creación de videos y reels',
                'mobile_alt' => 'Videos para móviles',
            ],
            [
                'subservicio_id' => 8,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#6366F1',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Diseños que comunican y enamoran',
                'left_alt' => 'Diseño UX y UI para redes',
                'right_alt' => 'Interfaces atractivas y funcionales',
                'mobile_alt' => 'Diseño UX UI móvil',
            ],

            // Servicio 3: Marketing y Gestión Digital
            [
                'subservicio_id' => 9,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#14B8A6',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Conocé a tu competencia y superala',
                'left_alt' => 'Análisis y benchmarking de mercado',
                'right_alt' => 'Estudio de competencia digital',
                'mobile_alt' => 'Análisis móvil',
            ],
            [
                'subservicio_id' => 10,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#D946EF',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Campañas que generan resultados reales',
                'left_alt' => 'Campañas digitales publicitarias',
                'right_alt' => 'Publicidad online efectiva',
                'mobile_alt' => 'Campañas para móviles',
            ],
            [
                'subservicio_id' => 11,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#FBBF24',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Construí una marca que nadie pueda olvidar',
                'left_alt' => 'Identidad visual y corporativa',
                'right_alt' => 'Branding y diseño de marca',
                'mobile_alt' => 'Identidad visual móvil',
            ],
            [
                'subservicio_id' => 12,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#22C55E',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Medí, analizá y optimizá cada acción',
                'left_alt' => 'Análisis de métricas y KPIs',
                'right_alt' => 'Reporting y dashboards',
                'mobile_alt' => 'Métricas para móviles',
            ],

            // Servicio 4: Branding y Diseño
            [
                'subservicio_id' => 13,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#FF6B35',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Empezá con el pie derecho, brief claro',
                'left_alt' => 'Desarrollo de brief creativo',
                'right_alt' => 'Brief para proyectos de diseño',
                'mobile_alt' => 'Brief para móviles',
            ],
            [
                'subservicio_id' => 14,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#06B6D4',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Planificación estratégica para tu marca',
                'left_alt' => 'Planificación estratégica de marca',
                'right_alt' => 'Estrategia de branding',
                'mobile_alt' => 'Planificación móvil',
            ],
            [
                'subservicio_id' => 15,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#EC4899',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Un logo que represente la esencia de tu marca',
                'left_alt' => 'Diseño de logo profesional',
                'right_alt' => 'Creación de identidad visual',
                'mobile_alt' => 'Logo para móviles',
            ],
            [
                'subservicio_id' => 16,
                'button_text' => 'HAZLO YA',
                'button_color' => '#6e26db',
                'service_color' => '#8B5CF6',
                'service_color_2' => '#F59E0B',
                'gradient_direction' => 'to bottom right',
                'trigger_time' => 5,
                'trigger_type' => 'time',
                'layout' => 'left-image',
                'show_logo' => true,
                'left_text' => 'Todas las reglas de tu marca en un solo lugar',
                'left_alt' => 'Manual de identidad corporativa',
                'right_alt' => 'Guía de uso de marca',
                'mobile_alt' => 'Manual de marca móvil',
            ],
        ];

        // Insertar pop-ups para subservicios
        foreach ($subserviciosConfigs as $config) {
            $subservicioId = $config['subservicio_id'];
            unset($config['subservicio_id']);
            
            $subservicio = Subservicio::find($subservicioId);
            
            if ($subservicio) {
                PopupConfig::updateOrCreate(
                    [
                        'popupable_type' => Subservicio::class,
                        'popupable_id' => $subservicioId
                    ],
                    $config
                );
                
                $this->command->info("Pop-up configurado para SUBSERVICIO ID: {$subservicioId}");
            } else {
                $this->command->warn("Subservicio ID {$subservicioId} no encontrado");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info("\nSeeding completado!");
        $this->command->info("Total pop-ups: " . PopupConfig::count());
    }
}