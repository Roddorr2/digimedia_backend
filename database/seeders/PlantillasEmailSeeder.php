<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillasEmailSeeder extends Seeder
{
    private const URL_CONTACTANOS = '/contactanos';
    private const FB = 'https://www.facebook.com/DigiMedia.Marketing1';
    private const IG = 'https://www.instagram.com/digimedia.pe/';
    private const TT = 'https://www.tiktok.com/@digimediamkt';
    private const LI = 'https://www.linkedin.com/company/digimedia-mkt/';
    private const CTA_TRABAJAR = 'Escríbenos y comencemos a trabajar';
    private const CTA_VER = 'Escríbenos y lo vemos contigo';

    public function run(): void
    {
        DB::table('plantillas_email')->insert($this->buildAll());
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

    private function build(int $serv, int $num): array
    {
        $data = $this->getData($serv, $num);
        return array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getData(int $s, int $n): array
    {
        $map = [
            '1-1' => ['IMPULSA TU ÉXITO ONLINE CON DIGIMEDIA! 🌐', 'Renueva tu web y conquista a tu competencia',
                '¡Hola {nombre}! 👋🏼<br><br>Te saludamos por parte del equipo de <strong>DIGIMEDIA 🚀</strong><br><br>Queremos contarte los principales beneficios que obtendrás con nuestro servicio de <strong>Diseño y Desarrollo Web</strong>:<br><br>✅ Una <strong>web profesional</strong> que genere confianza desde el primer contacto.<br>✅ Una <strong>experiencia clara y fácil de usar</strong> que ayude a atraer más clientes.<br>✅ <strong>Mayor visibilidad en Google</strong> para llegar a más personas interesadas en tu negocio.<br><br>Si estás buscando que tu sitio web <strong>apoye realmente el crecimiento de tu negocio</strong>, estaremos encantados de acompañarte en este proceso.<br><br>👉 <strong>Escríbenos y comencemos a trabajar en tu web.</strong>',
                self::CTA_TRABAJAR . ' en tu web'],
            '1-2' => ['¡FORTALECE TU PRESENCIA EN LÍNEA 💻!', 'Antes de que tus clientes huyan de tu web, lee esto.',
                '¡Hola {nombre}! 👋🏼<br><br>En <strong>DIGIMEDIA</strong> diseñamos y desarroll amos <strong>sitios web</strong> pensados para <strong>generar confianza y atraer clientes 🚀</strong><br><br><strong>Tu web como aliada de ventas</strong><br>Una página diseñada para <strong>convertir visitas en clientes</strong>.<br><br>Si quieres que tu web <strong>apoye el crecimiento de tu negocio</strong>, conversemos.<br><br>👉 <strong>Escríbenos y lo vemos contigo.</strong>',
                self::CTA_VER],
            '1-3' => ['¡MAXIMIZA TU PRESENCIA ONLINE! 💻', '¿Listo para incrementar el valor de tu marca?',
                '¡Hola {nombre}! 👋🏼<br><br>En <strong>DIGIMEDIA</strong> trabajamos <strong>sitios web</strong> pensados para <strong>apoyar el crecimiento real de tu negocio 🚀</strong><br><br>Con nuestro servicio de <strong>Diseño y Desarrollo Web</strong> obtendrás:<br><br>✅ Una <strong>web profesional</strong> que genere confianza desde el primer contacto.<br>✅ Una <strong>experiencia clara y fácil de usar</strong> que ayude a atraer más clientes.<br>✅ <strong>Mayor visibilidad en Google</strong> para llegar a más personas interesadas en tu negocio.<br>✅ Una página diseñada para <strong>convertir visitas en clientes</strong>.<br><br>Si quieres que tu web <strong>deje de ser solo informativa</strong> y empiece a <strong>trabajar para ti</strong>, conversemos.<br><br>👉 <strong>Escríbenos y lo vemos contigo.</strong>',
                self::CTA_VER],
            '2-1' => ['POTENCIA TU NEGOCIO DIGITAL CON EL PODER DIGIMEDIA', 'Haz crecer tu marca con estrategia🌐',
                '¡Hola {nombre}! 👋🏼<br><br>Te saludamos por parte del equipo de <strong>DIGIMEDIA 🚀</strong><br><br>Queremos contarte cómo nuestro servicio de <strong>Gestión de Redes Sociales</strong> puede ayudarte a <strong>fortalecer tu presencia digital</strong> y atraer más clientes:<br><br>✅ Redes sociales <strong>profesionales</strong> alineadas a la identidad de tu marca.<br>✅ <strong>Contenido estratégico y atractivo</strong> enfocado en atraer clientes.<br>✅ <strong>Mejor conexión con tu audiencia</strong> para fortalecer tu presencia digital.<br><br>Si buscas que tus redes sociales <strong>trabajen a favor de tu negocio</strong>, estaremos encantados de acompañarte en este proceso.<br><br>👉 <strong>Escríbenos y comencemos a trabajar tus redes sociales.</strong>',
                self::CTA_TRABAJAR],
            '2-2' => ['¡SUMÉRGETE EN EL MUNDO DIGITAL 📱!', '¡SUMÉRGETE EN EL MUNDO DIGITAL 📱!',
                '¡Hola {nombre}! 👋🏼<br><br>En <strong>DIGIMEDIA</strong> trabajamos la <strong>gestión de redes sociales</strong> para <strong>atraer clientes y generar crecimiento real 🚀</strong><br><br><strong>Resultados reales en redes</strong><br>Estrategia digital que <strong>conecta y convierte</strong>.<br><br>Si quieres que tus redes sociales <strong>apoyen el crecimiento de tu negocio</strong>, conversemos.<br><br>👉 <strong>Escríbenos y lo vemos contigo.</strong>',
                self::CTA_VER],
            '2-3' => ['¡AUMENTA TU PRESENCIA EN LAS REDES Y CONQUISTA NUEVAS AUDIENCIAS CON NOSOTROS! 💻🚀', '¿Publicas… pero no creces? tu estrategia necesita esto..',
                '¡Hola {nombre}! 👋🏼<br><br>En <strong>DIGIMEDIA</strong> gestionamos tus <strong>redes sociales</strong> para que <strong>realmente impulsen el crecimiento de tu negocio 🚀</strong><br><br>Con nuestro servicio de <strong>Gestión de Redes Sociales</strong> obtendrás:<br><br>✅ <strong>Contenido estratégico</strong> que conecte con tu audiencia y refuerce tu marca.<br>✅ <strong>Publicaciones consistentes</strong> y alineadas a tus objetivos comerciales.<br>✅ <strong>Mayor interacción y visibilidad</strong> en redes como Facebook e Instagram.<br>✅ Una <strong>comunidad activa</strong> que ayude a convertir seguidores en clientes.<br><br>Si quieres que tus redes <strong>dejen de ser solo publicaciones</strong> y empiecen a <strong>generar resultados reales</strong>, conversemos.<br><br>👉 <strong>Escríbenos y lo vemos contigo.</strong>',
                self::CTA_VER],
            '3-1' => ['¡CRECE TU NEGOCIO CON DIGIMEDIA 📈!', '💡Haz crecer y destacar tu negocio con DigiMedia.',
                '¡Hola {nombre}! 👋🏼<br><br>Te saludamos por parte del equipo de <strong>DIGIMEDIA 🚀</strong><br><br>Con nuestro servicio de <strong>Marketing y Gestión Digital</strong>, podrás lograr lo siguiente:<br><br>✅ Contar con una <strong>estrategia digital clara</strong> y enfocada en resultados.<br>✅ <strong>Atraer clientes ideales</strong> mediante acciones bien planificadas.<br>✅ <strong>Tomar mejores decisiones</strong> basadas en datos y métricas reales.<br><br>Trabajamos para que tu <strong>marketing digital</strong> tenga dirección, coherencia y <strong>genere crecimiento sostenible</strong>.<br><br>👉 <strong>Escríbenos y comencemos a impulsar tu crecimiento digital.</strong>',
                self::CTA_TRABAJAR],
            '3-2' => ['¡INNOVA EN TUS ESTRATEGIAS DIGITALES!', 'Lo que tu competencia ya está haciendo… y tú no',
                '¡Hola {nombre}! 👋🏼<br><br>En <strong>DIGIMEDIA</strong> definimos y gestionamos <strong>estrategias digitales</strong> para que <strong>tus acciones tengan dirección y generen resultados reales 🚀</strong><br><br><strong>Convierte tu estrategia en crecimiento</strong><br>Acciones claras basadas en <strong>datos reales</strong>.<br><br>Si quieres que tu <strong>marketing digital</strong> tenga orden y enfoque, conversemos.<br><br>👉 <strong>Escríbenos y lo vemos contigo.</strong>',
                self::CTA_VER],
            '3-3' => ['¡Aprovecha los beneficios del Mundo Digital! 💻 📈', 'Crece online o te quedas atrás. ¿Hablamos?',
                '¡Hola {nombre}! 👋🏼<br><br>En <strong>DIGIMEDIA</strong> trabajamos el <strong>marketing digital</strong> para que <strong>tus acciones tengan dirección y generen crecimiento real 🚀</strong><br><br>Con nuestro servicio de <strong>Marketing y Gestión Digital</strong> obtendrás:<br><br>✅ Contar con una <strong>estrategia digital clara</strong> y enfocada en resultados.<br>✅ <strong>Atraer clientes ideales</strong> mediante acciones bien planificadas.<br>✅ <strong>Tomar mejores decisiones</strong> basadas en datos y métricas reales.<br>✅ <strong>Acciones coherentes y medibles</strong> alineadas a tus objetivos de negocio.<br><br>Si quieres que tu marketing digital <strong>deje de improvisarse</strong> y empiece a <strong>generar resultados</strong>, conversemos.<br><br>👉 <strong>Escríbenos y lo vemos contigo.</strong>',
                self::CTA_VER],
            '4-1' => ['¡DESTACA TU NEGOCIO DIGITAL CON DIGIMEDIA! 🙌🏼', '🚀 ¡Haz que tu marca despegue con DigiMedia!',
                '¡Hola {nombre}! 👋🏼<br><br>Te saludamos por parte del equipo de <strong>DIGIMEDIA 🚀</strong><br><br>Nuestro servicio de <strong>Branding e Identidad Visual</strong> está diseñado para ayudarte a:<br><br>✅ Construir una <strong>identidad de marca clara y bien definida</strong> que genere confianza al vender.<br>✅ Hacer crecer tu marca con una <strong>estrategia pensada para atraer y convertir</strong>.<br>✅ Obtener <strong>resultados reales</strong> gracias a una imagen potente y métricas relevantes.<br><br>Creemos <strong>marcas coherentes, profesionales</strong> y alineadas a <strong>objetivos reales de negocio</strong>.<br><br>👉 <strong>Escríbenos y comencemos a construir una marca sólida.</strong>',
                self::CTA_TRABAJAR],
            '4-2' => ['¡MARCA LA DIFERENCIA! 😉', 'Destaca entre tu competencia con una marca que impacte',
                '¡Hola {nombre}! 👋🏼<br><br>En <strong>DIGIMEDIA</strong> trabajamos la <strong>identidad de tu marca</strong> para que <strong>se vea profesional, comunique con claridad y genere confianza desde el primer contacto 🚀</strong><br><br><strong>Construye una marca sólida</strong><br>Identidad clara con <strong>impacto real</strong>.<br><br>Si quieres que tu marca <strong>se vea coherente y bien definida</strong>, conversemos.<br><br>👉 <strong>Escríbenos y lo vemos contigo.</strong>',
                self::CTA_VER],
            '4-3' => ['¡TEN UNA IDENTIDAD ÚNICA! 😉', 'Tu marca merece destacar. ¡ Hazlo inolvidable con DIGIMEDIA!',
                '¡Hola {nombre}! 👋🏼<br><br>En <strong>DIGIMEDIA</strong> trabajamos la <strong>identidad de marca</strong> para que <strong>se vea profesional, comunique con claridad y apoye el crecimiento real de tu negocio 🚀</strong><br><br>Con nuestro servicio de <strong>Branding e Identidad Visual</strong> obtendrás:<br><br>✅ Construir una <strong>identidad de marca clara y bien definida</strong> que genere confianza al vender.<br>✅ Hacer crecer tu marca con una <strong>estrategia pensada para atraer y convertir</strong>.<br>✅ Obtener <strong>resultados reales</strong> gracias a una imagen potente y métricas relevantes.<br>✅ Una <strong>imagen coherente y consistente</strong> alineada a tus objetivos de negocio.<br><br>Si quieres que tu marca <strong>deje de verse improvisada</strong> y empiece a <strong>comunicar con coherencia y propósito</strong>, conversemos.<br><br>👉 <strong>Escríbenos y lo vemos contigo.</strong>',
                self::CTA_VER],
        ];

        $imgs = [
            [1 => 'desarrollo-diseño/flyer-modal-1-1-v2.jpg', 2 => 'desarrollo-diseño/flyer-modal-1-2-v2.jpg', 3 => 'desarrollo-diseño/flyer-modal-1-3-v2.jpg'],
            [1 => 'gestion-redes/flyer-modal-2-1-v2.jpg', 2 => 'gestion-redes/flyer-modal-2-2-v2.jpg', 3 => 'gestion-redes/flyer-modal-2-3-v2.jpg'],
            [1 => 'marketing-gestion/flyer-modal-3-1-v2.jpg', 2 => 'marketing-gestion/flyer-modal-3-2-v2.jpg', 3 => 'marketing-gestion/flyer-modal-3-3-v2.jpg'],
            [1 => 'branding-diseño/flyer-modal-4-1-v2.jpg', 2 => 'branding-diseño/flyer-modal-4-2-v2.jpg', 3 => 'branding-diseño/flyer-modal-4-3-v2.jpg'],
        ];

        $srvNames = [1 => 'Diseño Web', 2 => 'Redes Sociales', 3 => 'Marketing Digital', 4 => 'Branding'];
        $key = "{$s}-{$n}";
        [$asunto, $encabezado, $mensaje, $cta] = $map[$key];

        return [
            'id_servicio' => $s,
            'numero_plantilla' => $n,
            'nombre' => $srvNames[$s] . " - Email {$n}",
            'asunto' => $asunto,
            'encabezado' => $encabezado,
            'imagen_url' => url('assets/images/' . $imgs[$s - 1][$n]),
            'mensaje' => $mensaje,
            'mensaje_boton' => $cta,
            'url_boton' => url(self::URL_CONTACTANOS),
            'footer' => 'Quedamos atentos a tu mensaje.<br>Saludos,<br>Equipo Digimedia',
            'red_facebook' => self::FB,
            'red_tiktok' => self::TT,
            'red_instagram' => self::IG,
            'red_linkedin' => self::LI,
        ];
    }
}
