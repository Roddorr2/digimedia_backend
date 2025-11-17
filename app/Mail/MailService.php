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
                "POTENCIA TU NEGOCIO DIGITAL CON DIGIMEDIA",
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
                "Haz crecer tu marca con estrategia",
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
                "En Digimedia diseñamos experiencias digitales que combinan <strong>diseño atractivo, carga rápida y navegación intuitiva</strong>, adaptadas a cada tipo de cliente.<br><br>
                Tu web no solo debe verse bien, debe <strong>funcionar y convertir.<br><br>
                📈 Haz que tu presencia digital sea tan sólida como tu esfuerzo.<br><br>
                🚀 Agenda tu asesoría gratuita hoy mismo</strong><br><br>
                y descubre cómo convertir tu página en una aliada para tu crecimiento.",

                "¿Sabías que un cliente decide en menos de <strong>5 segundos</strong> si confía en tu marca… solo por tu página web? ⏳💻<br><br>
                Si tu sitio es lento, desordenado o poco profesional, tu marca pierde <strong>credibilidad</strong> y oportunidades de venta sin que lo notes. ❌📉<br><br>
                Tu web no es solo un espacio digital… es tu vitrina principal. 🛍️✨<br>
                <strong>¿Está transmitiendo lo que realmente quieres?</strong><br><br>
                <strong>👉 Haz que tu web impacte desde el primer segundo.</strong><br>
                Tu asesoría gratuita te espera. 🚀",

                "Creemos en el cambio como parte clave del crecimiento de tu marca. Tus clientes cambian a diario, tu página web debe seguirles el ritmo.<br>
                Que tu página web no se limite a un buen diseño y un buen eslogan; hoy en día tus clientes valoran la conexión que pueden lograr contigo.<br><br>
                En Digimedia te damos los conocimientos y las herramientas para mantener tu marca actualizada."
            ],
            // Gestión de Redes Sociales
            [
                "Te ayudamos a crear una presencia digital sólida, con contenido coherente.<br><br>
                Con una gestión estratégica de redes sociales, tu negocio puede lograr mucho más que solo likes.<br><br>
                ✅ Aumenta tu visibilidad ante el público correcto.<br>
                ✅ Crea contenido que conecte y genere confianza.<br>
                ✅ Transforma seguidores en clientes reales.<br><br>
                Hagamos que tu marca destaque y genere conversación cada día.<br><br>
                Reserva tu <strong>asesoría gratuita</strong> hoy mismo",

                "<strong>DigiMedia Marketing</strong><br><br>
                <strong>¡Haz que tu marca enamore desde el primer vistazo! ✨</strong><br>
                En un mundo lleno de información, solo los contenidos bien hechos logran destacar.<br><br>
                Por eso en <strong>DigiMedia</strong> creamos <strong>contenidos que atraen, conquistan y abren puertas</strong> para que tu negocio conecte con las personas correctas.<br>
                Potencia tu presencia digital con ideas frescas, mensajes claros y piezas visuales diseñadas para generar resultados reales.<br><br>
                <strong>💡 ¿Lo mejor? Tu primera asesoría es completamente GRATIS.</strong><br>
                Descubre cómo podemos transformar tu comunicación y llevar tu marca al siguiente nivel.",

                "Sabemos que gestionar redes sociales solo puede ser complicado: crear contenido, mantener constancia y entender qué funciona… agota a cualquiera.<br><br>
                ✨ Pero potenciar tus redes va más allá de “estar presente”.<br>
                🎯 Con una estrategia real, tus publicaciones dejan de ser un esfuerzo perdido y se convierten en oportunidades para atraer clientes y generar confianza.<br><br>
                En Digimedia te ayudamos con:<br>
                <ul style='margin-top: 0; margin-bottom: 0; padding-left: 20px;'>
                <li>Contenido que conecta.</li>
                <li>Estrategias según tu público y objetivos.</li>
                <li>Optimización constante.</li>
                <li>Mayor visibilidad y mejores resultados.</li>
                </ul>
                <p style='margin: 0; padding: 0; line-height: 25px; mso-line-height-rule: exactly;'>&nbsp;</p>
                🚀 Tu crecimiento no debería depender del algoritmo.<br>
                🤝 Deja que te guiemos para que tus redes trabajen por ti.<br>
                👉 Tu crecimiento comienza con un solo clic. ¡Aprovecha la asesoría gratuita!"
            ],
            // Marketing y Gestión Digital
            [
                "En un mundo donde todos quieren ser notados, <strong>asegura que tu marca no solo sea vista, sino recordada.</strong><br><br>
                📈 Nuestro equipo está preparado para llevar tu presencia online al siguiente nivel con soluciones creativas, efectivas y medibles.<br><br>
                ✨ <strong>Beneficios exclusivos:</strong><br>
                <ul style='margin-top: 0; margin-bottom: 0; padding-left: 20px;'>
                <li>Mayor visibilidad y posicionamiento online.</li>
                <li>Estrategias que generan conexión y lealtad con tus clientes.</li>
                <li>Presencia digital sólida y diferenciada.</li>
                </ul>
                <p style='margin: 0; padding: 0; line-height: 25px; mso-line-height-rule: exactly;'>&nbsp;</p>
                🚀 ¡No te quedes atrás en la era digital! Transforma tu negocio con estrategias innovadoras diseñadas para maximizar tu rentabilidad.<br><br>
                📩 Contáctanos hoy y comencemos a construir tu crecimiento digital.",

                "<strong>¿Quieres que tu negocio por fin despegue en el mundo digital?</strong><br>
                En <strong>DigiMedia</strong> tenemos las estrategias exactas para que tu marca gane visibilidad, clientes y resultados reales. Dominamos el entorno online y te acompañamos para que tu presencia digital sea sólida, atractiva y rentable.<br>
                Si buscas crecer, digitalizarte y generar mayores ganancias, este es el momento.<br><br>
                ¡Contáctanos ahora!",

                "En DigiMedia transformamos tu presencia online con estrategias que sí venden.<br><br>
                ✨ ¿Qué logramos contigo?<br>
                <ul style='margin-top: 0; margin-bottom: 0; padding-left: 20px;'>
                <li>Clientes más fieles</li>
                <li>Publicidad digital que convierte</li>
                <li>Estrategias a tu medida</li>
                <li>Mejor rendimiento de tu inversión</li>
                </ul>
                <p style='margin: 0; padding: 0; line-height: 25px; mso-line-height-rule: exactly;'>&nbsp;</p>
                Digitaliza tu negocio y empieza a ver resultados reales.<br>
                Escríbenos y activemos tu crecimiento hoy. 🚀"
            ],
            // Branding y Diseño
            [
                "¿Sientes que tu negocio se pierde entre la competencia?<br>
                En <strong>DigiMedia</strong>, te ayudamos a <strong>mantenerte en la mente de tus clientes.</strong> No se trata solo de estar presente, sino de ser <strong>relevante.</strong><br><br>
                ✨ Somos especialistas en <strong>branding cautivador, diseño irresistible y asesorías personalizadas</strong> que harán que tu marca refleje lo que realmente representa.<br><br>
                💡 <strong>Beneficios que obtendrás:</strong><br>
                <ul style='margin-top: 0; margin-bottom: 0; padding-left: 20px;'>
                <li>Mayor reconocimiento y diferenciación en tu rubro.</li>
                <li>Identidad visual coherente y profesional.</li>
                <li>Estrategias que fortalecen la conexión con tus clientes.</li>
                </ul>
                <p style='margin: 0; padding: 0; line-height: 25px; mso-line-height-rule: exactly;'>&nbsp;</p>
                📩 Conoce cómo transformar tu presencia digital y llevar tu negocio al siguiente nivel.<br><br>
                👉 <strong>No seas uno más, sé relevante.</strong>",

                "¿Tu marca está dejando huella o solo pasando desapercibida?<br>
                En DIGIMEDIA te ayudamos a construir una identidad visual fuerte, coherente y auténtica, para que tu negocio destaque entre la competencia y conecte con sus clientes.<br>
                Con nuestro servicio de Branding y Diseño, obtendrás:<br>
                <ul style='margin-top: 0; margin-bottom: 0; padding-left: 20px;'>
                <li>Identidad visual profesional que refleje los valores de tu marca.</li>
                <li>Conceptos creativos y originales que te diferencien del resto.</li>
                <li>Posicionamiento visual que inspire confianza y recordación.</li>
                </ul>
                <p style='margin: 0; padding: 0; line-height: 25px; mso-line-height-rule: exactly;'>&nbsp;</p>",

                "¿Tu marca transmite lo que realmente quieres decir?<br>
                En DIGIMEDIA te ayudamos a construir una identidad única y profesional que conecte con tu público desde el primer vistazo.<br>
                Nuestro servicio de Branding y Diseño te permitirá:<br>
                <ul style='margin-top: 0; margin-bottom: 0; padding-left: 20px;'>
                <li>Crear una identidad visual auténtica y coherente.</li>
                <li>Ganar confianza y credibilidad en el entorno digital.</li>
                <li>Diferenciarte de tu competencia y posicionarse con fuerza.</li>
                </ul>
                <p style='margin: 0; padding: 0; line-height: 25px; mso-line-height-rule: exactly;'>&nbsp;</p>"
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
