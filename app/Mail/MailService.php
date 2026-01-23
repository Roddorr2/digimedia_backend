<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailService extends Mailable
{
    use Queueable, SerializesModels;
    public int $number_message;
    public $data;
    public $id_service;

    public function __construct($number_message, $data, $id_service)
    {
        $this->number_message = $number_message;
        $this->data = $data;
        $this->id_service = $id_service;
    }

    public function build()
    {


        $images = [
            // Desarrollo y Diseño
            [
                url('assets/images/desarrollo-diseño/flyer-modal-1-1.jpg'),
                url('assets/images/desarrollo-diseño/flyer-modal-1-2.jpg'),
                url('assets/images/desarrollo-diseño/flyer-modal-1-3.jpg')
            ],
            // Gestión de Redes Sociales
            [
                url('assets/images/gestion-redes/flyer-modal-2-1.jpg'),
                url('assets/images/gestion-redes/flyer-modal-2-2.jpg'),
                url('assets/images/gestion-redes/flyer-modal-2-3.jpg')
            ],
            // Marketing y Gestión Digital
            [
                url('assets/images/marketing-gestion/flyer-modal-3-1.jpg'),
                url('assets/images/marketing-gestion/flyer-modal-3-2.jpg'),
                url('assets/images/marketing-gestion/flyer-modal-3-3.jpg')
            ],
            // Branding y Diseño
            [
                url('assets/images/branding-diseño/flyer-modal-4-1.jpg'),
                url('assets/images/branding-diseño/flyer-modal-4-2.jpg'),
                url('assets/images/branding-diseño/flyer-modal-4-3.jpg')
            ]
        ];

        $subject = [
            // Desarrollo y Diseño
            [
                "IMPULSA TU ÉXITO ONLINE CON DIGIMEDIA! 🌐",
                "¡FORTALECE TU PRESENCIA EN LÍNEA 💻!",
                "¡MAXIMIZA TU PRESENCIA ONLINE! 💻"
            ],
            // Gestión de Redes Sociales
            [
                "POTENCIA TU NEGOCIO DIGITAL CON EL PODER DIGIMEDIA",
                "¡SUMÉRGETE EN EL MUNDO DIGITAL 📱!",
                "¡AUMENTA TU PRESENCIA EN LAS REDES Y CONQUISTA NUEVAS AUDIENCIAS CON NOSOTROS! 💻🚀 "
            ],
            // Marketing y Gestión Digital
            [
                "¡CRECE TU NEGOCIO CON DIGIMEDIA 📈!",
                "¡INNOVA EN TUS ESTRATEGIAS DIGITALES!",
                "¡Aprovecha los beneficios del Mundo Digital! 💻 📈"
            ],
            // Branding y Diseño
            [
                "¡DESTACA TU NEGOCIO DIGITAL CON DIGIMEDIA! 🙌🏼",
                "¡MARCA LA DIFERENCIA! 😉",
                "¡TEN UNA IDENTIDAD ÚNICA! 😉"
            ]
        ];

        $title = [
            // Desarrollo y Diseño
            [
                "Renueva tu web y conquista a tu competencia",
                "Antes de que tus clientes huyan de tu web, lee esto.",
                "¿Listo para incrementar el valor de tu marca?"
            ],
            // Gestión de Redes Sociales
            [
                "Haz crecer tu marca con estrategia🌐",
                "¡SUMÉRGETE EN EL MUNDO DIGITAL 📱!",
                "¿Publicas… pero no creces? tu estrategia necesita esto.."
            ],
            // Marketing y Gestión Digital
            [
                "💡Haz crecer y destacar tu negocio con DigiMedia.",
                "Lo que tu competencia ya está haciendo… y tú no",
                "Crece online o te quedas atrás. ¿Hablamos?"
            ],
            // Branding y Diseño
            [
                "🚀 ¡Haz que tu marca despegue con DigiMedia!",
                "Destaca entre tu competencia con una marca que impacte",
                "Tu marca merece destacar. ¡ Hazlo inolvidable con DIGIMEDIA!"
            ]
        ];

        $message = [
            // Desarrollo y Diseño
            [
                "Te saludamos por parte del equipo de <strong>DIGIMEDIA 🚀</strong><br><br>
                Queremos contarte los principales beneficios que obtendrás con nuestro servicio de 
                <strong>Diseño y Desarrollo Web</strong>:<br><br>
                ✅ Una <strong>web profesional</strong> que genere confianza desde el primer contacto.<br>
                ✅ Una <strong>experiencia clara y fácil de usar</strong> que ayude a atraer más clientes.<br>
                ✅ <strong>Mayor visibilidad en Google</strong> para llegar a más personas interesadas en tu negocio.<br><br>
                Si estás buscando que tu sitio web <strong>apoye realmente el crecimiento de tu negocio</strong>, 
                estaremos encantados de acompañarte en este proceso.<br><br>
                👉 <strong>Escríbenos y comencemos a trabajar en tu web.</strong>",

                "En <strong>DIGIMEDIA</strong> diseñamos y desarrollamos <strong>sitios web</strong> pensados para 
                <strong>generar confianza y atraer clientes 🚀</strong><br><br>
                <strong>Tu web como aliada de ventas</strong><br>
                Una página diseñada para <strong>convertir visitas en clientes</strong>.<br><br>
                Si quieres que tu web <strong>apoye el crecimiento de tu negocio</strong>, conversemos.<br><br>
                👉 <strong>Escríbenos y lo vemos contigo.</strong>",

                "En <strong>DIGIMEDIA</strong> trabajamos <strong>sitios web</strong> pensados para 
                <strong>apoyar el crecimiento real de tu negocio 🚀</strong><br><br>
                Con nuestro servicio de <strong>Diseño y Desarrollo Web</strong> obtendrás:<br><br>
                ✅ Una <strong>web profesional</strong> que genere confianza desde el primer contacto.<br>
                ✅ Una <strong>experiencia clara y fácil de usar</strong> que ayude a atraer más clientes.<br>
                ✅ <strong>Mayor visibilidad en Google</strong> para llegar a más personas interesadas en tu negocio.<br>
                ✅ Una página diseñada para <strong>convertir visitas en clientes</strong>.<br><br>
                Si quieres que tu web <strong>deje de ser solo informativa</strong> y empiece a 
                <strong>trabajar para ti</strong>, conversemos.<br><br>
                👉 <strong>Escríbenos y lo vemos contigo.</strong>"
            ],
            // Gestión de Redes Sociales
            [
                "Te saludamos por parte del equipo de <strong>DIGIMEDIA 🚀</strong><br><br>
                Queremos contarte cómo nuestro servicio de 
                <strong>Gestión de Redes Sociales</strong> puede ayudarte a 
                <strong>fortalecer tu presencia digital</strong> y atraer más clientes:<br><br>
                ✅ Redes sociales <strong>profesionales</strong> alineadas a la identidad de tu marca.<br>
                ✅ <strong>Contenido estratégico y atractivo</strong> enfocado en atraer clientes.<br>
                ✅ <strong>Mejor conexión con tu audiencia</strong> para fortalecer tu presencia digital.<br><br>
                Si buscas que tus redes sociales <strong>trabajen a favor de tu negocio</strong>, 
                estaremos encantados de acompañarte en este proceso.<br><br>
                👉 <strong>Escríbenos y comencemos a trabajar tus redes sociales.</strong>",

                "En <strong>DIGIMEDIA</strong> trabajamos la <strong>gestión de redes sociales</strong> para 
                <strong>atraer clientes y generar crecimiento real 🚀</strong><br><br>
                <strong>Resultados reales en redes</strong><br>
                Estrategia digital que <strong>conecta y convierte</strong>.<br><br>
                Si quieres que tus redes sociales <strong>apoyen el crecimiento de tu negocio</strong>, 
                conversemos.<br><br>
                👉 <strong>Escríbenos y lo vemos contigo.</strong>",

                "En <strong>DIGIMEDIA</strong> gestionamos tus <strong>redes sociales</strong> para que 
                <strong>realmente impulsen el crecimiento de tu negocio 🚀</strong><br><br>
                Con nuestro servicio de <strong>Gestión de Redes Sociales</strong> obtendrás:<br><br>
                ✅ <strong>Contenido estratégico</strong> que conecte con tu audiencia y refuerce tu marca.<br>
                ✅ <strong>Publicaciones consistentes</strong> y alineadas a tus objetivos comerciales.<br>
                ✅ <strong>Mayor interacción y visibilidad</strong> en redes como Facebook e Instagram.<br>
                ✅ Una <strong>comunidad activa</strong> que ayude a convertir seguidores en clientes.<br><br>
                Si quieres que tus redes <strong>dejen de ser solo publicaciones</strong> y empiecen a 
                <strong>generar resultados reales</strong>, conversemos.<br><br>
                👉 <strong>Escríbenos y lo vemos contigo.</strong>"
            ],
            // Marketing y Gestión Digital
            [
                "Te saludamos por parte del equipo de <strong>DIGIMEDIA 🚀</strong><br><br>
                Con nuestro servicio de <strong>Marketing y Gestión Digital</strong>, podrás lograr lo siguiente:<br><br>
                ✅ Contar con una <strong>estrategia digital clara</strong> y enfocada en resultados.<br>
                ✅ <strong>Atraer clientes ideales</strong> mediante acciones bien planificadas.<br>
                ✅ <strong>Tomar mejores decisiones</strong> basadas en datos y métricas reales.<br><br>
                Trabajamos para que tu <strong>marketing digital</strong> tenga dirección, coherencia y 
                <strong>genere crecimiento sostenible</strong>.<br><br>
                👉 <strong>Escríbenos y comencemos a impulsar tu crecimiento digital.</strong>",

                "En <strong>DIGIMEDIA</strong> definimos y gestionamos <strong>estrategias digitales</strong> para que 
                <strong>tus acciones tengan dirección y generen resultados reales 🚀</strong><br><br>
                <strong>Convierte tu estrategia en crecimiento</strong><br>
                Acciones claras basadas en <strong>datos reales</strong>.<br><br>
                Si quieres que tu <strong>marketing digital</strong> tenga orden y enfoque, 
                conversemos.<br><br>
                👉 <strong>Escríbenos y lo vemos contigo.</strong>",

                "En <strong>DIGIMEDIA</strong> trabajamos el <strong>marketing digital</strong> para que 
                <strong>tus acciones tengan dirección y generen crecimiento real 🚀</strong><br><br>
                Con nuestro servicio de <strong>Marketing y Gestión Digital</strong> obtendrás:<br><br>
                ✅ Contar con una <strong>estrategia digital clara</strong> y enfocada en resultados.<br>
                ✅ <strong>Atraer clientes ideales</strong> mediante acciones bien planificadas.<br>
                ✅ <strong>Tomar mejores decisiones</strong> basadas en datos y métricas reales.<br>
                ✅ <strong>Acciones coherentes y medibles</strong> alineadas a tus objetivos de negocio.<br><br>
                Si quieres que tu marketing digital <strong>deje de improvisarse</strong> y empiece a 
                <strong>generar resultados</strong>, conversemos.<br><br>
                👉 <strong>Escríbenos y lo vemos contigo.</strong>"
            ],
            // Branding y Diseño
            [
                "Te saludamos por parte del equipo de <strong>DIGIMEDIA 🚀</strong><br><br>
                Nuestro servicio de <strong>Branding e Identidad Visual</strong> está diseñado para ayudarte a:<br><br>
                ✅ Construir una <strong>identidad de marca clara y bien definida</strong> que genere confianza al vender.<br>
                ✅ Hacer crecer tu marca con una <strong>estrategia pensada para atraer y convertir</strong>.<br>
                ✅ Obtener <strong>resultados reales</strong> gracias a una imagen potente y métricas relevantes.<br><br>
                Creemos <strong>marcas coherentes, profesionales</strong> y alineadas a 
                <strong>objetivos reales de negocio</strong>.<br><br>
                👉 <strong>Escríbenos y comencemos a construir una marca sólida.</strong>",

                "En <strong>DIGIMEDIA</strong> trabajamos la <strong>identidad de tu marca</strong> para que 
                <strong>se vea profesional, comunique con claridad y genere confianza desde el primer contacto 🚀</strong><br><br>
                <strong>Construye una marca sólida</strong><br>
                Identidad clara con <strong>impacto real</strong>.<br><br>
                Si quieres que tu marca <strong>se vea coherente y bien definida</strong>, 
                conversemos.<br><br>
                👉 <strong>Escríbenos y lo vemos contigo.</strong>",

                "En <strong>DIGIMEDIA</strong> trabajamos la <strong>identidad de marca</strong> para que 
                <strong>se vea profesional, comunique con claridad y apoye el crecimiento real de tu negocio 🚀</strong><br><br>
                Con nuestro servicio de <strong>Branding e Identidad Visual</strong> obtendrás:<br><br>
                ✅ Construir una <strong>identidad de marca clara y bien definida</strong> que genere confianza al vender.<br>
                ✅ Hacer crecer tu marca con una <strong>estrategia pensada para atraer y convertir</strong>.<br>
                ✅ Obtener <strong>resultados reales</strong> gracias a una imagen potente y métricas relevantes.<br>
                ✅ Una <strong>imagen coherente y consistente</strong> alineada a tus objetivos de negocio.<br><br>
                Si quieres que tu marca <strong>deje de verse improvisada</strong> y empiece a 
                <strong>comunicar con coherencia y propósito</strong>, conversemos.<br><br>
                👉 <strong>Escríbenos y lo vemos contigo.</strong>"
            ]
        ];

        $message_send = $message[$this->id_service - 1][$this->number_message - 1];
        $image_send = $images[$this->id_service - 1][$this->number_message - 1];
        $title_send = $title[$this->id_service - 1][$this->number_message - 1];
        $subject_send = $subject[$this->id_service - 1][$this->number_message - 1];

        return $this->subject($subject_send)
                ->view('mails.modal')
                ->with([
                    'data' => $this->data,
                    'send_message' => $message_send,
                    'title' => $title_send,
                    'image' => $image_send,
                    'id_service' => $this->id_service,
                ]);
    }
}
