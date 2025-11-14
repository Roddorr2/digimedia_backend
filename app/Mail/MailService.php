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
                "¡Aprovecha los beneficios del mundo digital! 👩🏻💻🖥 "
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
                "¡FORTALECE TU PRESENCIA EN LÍNEA 💻!",
                "¡MAXIMIZA TU PRESENCIA ONLINE! 💻"
            ],
            // Gestión de Redes Sociales
            [
                "Haz crecer tu marca con estrategia",
                "¡SUMÉRGETE EN EL MUNDO DIGITAL 📱!",
                "¡AUMENTA TU PRESENCIA EN LAS REDES Y CONQUISTA NUEVAS AUDIENCIAS CON NOSOTROS! 💻🚀 "
            ],
            // Marketing y Gestión Digital
            [
                "💡Haz crecer y destacar tu negocio con DigiMedia.",
                "¡INNOVA EN TUS ESTRATEGIAS DIGITALES!",
                "¡Aprovecha los beneficios del mundo digital! 👩🏻💻🖥 "
            ],
            // Branding y Diseño
            [
                "🚀 ¡Haz que tu marca despegue con DigiMedia!",
                "¡MARCA LA DIFERENCIA! 😉",
                "¡TEN UNA IDENTIDAD ÚNICA! 😉"
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

                "En Digimedia te ayudamos a crecer con una identidad visual sólida para tu marca, experiencias digitales diseñadas para convertir visitas en ventas y estrategias innovadoras que generan resultados reales.<br><br>
                Lo que obtendrás con nosotros:<br><br>
                🌐 Visuales que cautivan desde el primer segundo.<br>
                ⚡Estética que inspira y conecta.<br>
                📈 Imágenes que hablan por ti.",

                "Impulsamos tu éxito digital con beneficios exclusivos: mayor visibilidad y tráfico web, experiencias de usuario que convierten visitantes en clientes leales y estrategias innovadoras que harán que tu negocio brille en la web.<br><br>
                En Digimedia te damos las herramientas para lograrlo con:<br><br>
                🔍 Visibilidad digital que atrae más clientes.<br>
                🎨 Identidad de marca reflejada en cada detalle.<br>
                🚀 Tecnología actualizada para un sitio rápido y seguro."
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

                "¿Quieres tener contenido de calidad? Deja la gestión de tus redes sociales en manos expertas con DigiMedia y haz crecer tu negocio de la mejor manera con nuestros beneficios: \n\n
                - Planificación y organización de contenidos.\n
                - Análisis de resultados con informes mensuales.",

                "¿Buscas contenido de alto impacto? Confía en los especialistas de DigiMedia Marketing para gestionar tus redes sociales y lleva tu negocio al siguiente nivel con nuestro servicio de Gestión Redes Sociales.\n\n
                ✅ Diseño estratégico y calendario de contenido en redes.
                ✅ Análisis de desempeño con reportes mensuales y más!"
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

                "¿Quieres tener las mejores estrategias online de marketing? En DigiMedia somos expertos dominando el mundo digital y juntos potenciaremos tu presencia digital. \n\n
                - Vínculo de lealtad con los clientes\n
                - Desarrollar publicidad en línea\n\n
                💻 Obtén mayores ganancias digitalizando tu negocio junto a DigiMedia Marketing 💰📈. Con el servicio de marketing y gestión digital podrás tener:\n\n
                ✅ Estrategias digitales personalizadas.\n
                ✅ Mejor rendimiento de tu presupuesto.\n\n
                Comunícate con nosotros/responde este mensaje para obtener más información y comienza a ver resultados.",

                "¿Quieres tener las mejores estrategias online de marketing? En DigiMedia somos expertos dominando el mundo digital y juntos potenciaremos tu presencia digital.\n\n
                - Vínculo de lealtad con los clientes\n
                - Desarrollar publicidad en línea\n\n
                💻 Obtén mayores ganancias digitalizando tu negocio junto a DigiMedia Marketing 💰📈. Con el servicio de marketing y gestión digital podrás tener:\n\n
                ✅ Estrategias digitales personalizadas.\n
                ✅ Mejor rendimiento de tu presupuesto.\n\n
                Comunícate con nosotros/responde este mensaje para obtener más información y comienza a ver resultados.",
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

                "¿Quieres destacar entre tu competencia? Con DigiMedia podrás tener una marca sólida gracias a nuestro servicio de Branding y diseño que te ayudarán a ser reconocida entre tus clientes 🚀.\n\n
                - ✅ Desarrollo en identidad visual de tu marca\n
                - ✅ Originalidad en conceptos de marca ",

                "En DigiMedia garantizamos crear experiencias visuales impactantes y memorables para que puedas conectar con tu audiencia, nuestro servicio de Branding y diseño te ayudarán a lograr esto 🚀. Nuestros beneficios:\n\n
                - Originalidad en conceptos de marca\n
                - Construcción de confianza y credibilidad\n\n
                ¡Sé parte del mundo digital y potencia tu marca con nosotros! 🙌🏼"
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
