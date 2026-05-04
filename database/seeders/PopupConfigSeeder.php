<?php

namespace Database\Seeders;

use App\Models\PopupConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PopupConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // Servicio 1: Diseño Web y Desarrollo Web
            ['id_subservicio' => 1, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#a855f7', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5, 'left_alt' => 'Icono de diseño y desarrollo web', 'right_alt' => 'Servicios de diseño web responsivo', 'mobile_alt' => 'Diseño web para dispositivos móviles'],
            ['id_subservicio' => 2, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#a855f7', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 3, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#a855f7', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 4, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#a855f7', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            
            // Servicio 2: Gestión de Redes Sociales
            ['id_subservicio' => 5, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#06b6d4', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 6, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#06b6d4', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 7, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#06b6d4', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 8, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#06b6d4', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            
            // Servicio 3: Marketing y Gestión Digital
            ['id_subservicio' => 9, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#f59e0b', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 10, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#f59e0b', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 11, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#f59e0b', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 12, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#f59e0b', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            
            // Servicio 4: Branding y Diseño
            ['id_subservicio' => 13, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#ec4899', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 14, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#ec4899', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 15, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#ec4899', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
            ['id_subservicio' => 16, 'title_text' => 'OBTÉN UNA ASESORÍA ¡GRATIS!', 'title_color' => '#FFFFFF', 'button_text' => 'HAZLO YA', 'button_color' => '#7C3FD9', 'service_color' => '#8B5CF6', 'service_color_2' => '#ec4899', 'gradient_direction' => 'to bottom right', 'trigger_time' => 5],
        ];

        foreach ($configs as $config) {
            PopupConfig::updateOrCreate(
                ['id_subservicio' => $config['id_subservicio']],
                $config
            );
        }
    }
}
