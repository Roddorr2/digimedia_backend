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

        $title = [
            // Desarrollo y Diseño
            [
                "¡IMPULSA TU ÉXITO ONLINE CON DIGIMEDIA! 🌐",
                "¡FORTALECE TU PRESENCIA EN LÍNEA 💻!",
                "¡MAXIMIZA TU PRESENCIA ONLINE! 💻"
            ],
            // Gestión de Redes Sociales
            [
                "¡POTENCIA TU NEGOCIO DIGITAL CON DIGIMEDIA! 📈",
                "¡SUMÉRGETE EN EL MUNDO DIGITAL 📱!",
                "¡AUMENTA TU PRESENCIA EN LAS REDES Y CONQUISTA NUEVAS AUDIENCIAS CON NOSOTROS! 💻🚀 "
            ],
            // Marketing y Gestión Digital
            [
                "¡CRECE TU NEGOCIO CON DIGIMEDIA!📈",
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

        $subject = [
            // Desarrollo y Diseño

            [
                "IMPULSA TU ÉXITO ONLINE CON DIGIMEDIA! 🌐",
                "¡FORTALECE TU PRESENCIA EN LÍNEA 💻!",
                "¡MAXIMIZA TU PRESENCIA ONLINE! 💻"
            ],
            // Gestión de Redes Sociales
            [
                "POTENCIA TU NEGOCIO DIGITAL CON DIGIMEDIA 📈",
                "¡SUMÉRGETE EN EL MUNDO DIGITAL 📱!",
                "¡AUMENTA TU PRESENCIA EN LAS REDES Y CONQUISTA NUEVAS AUDIENCIAS CON NOSOTROS! 💻🚀 "
            ],
            // Marketing y Gestión Digital
            [
                "¡CRECE TU NEGOCIO CON DIGIMEDIA 📈!",
                "¡INNOVA EN TUS ESTRATEGIAS DIGITALES!",
                "¡Aprovecha los beneficios del Mundo Digital! 👩🏻💻🖥 * "
            ],
            // Branding y Diseño
            [
                "¡DESTACA TU NEGOCIO DIGITAL CON DIGIMEDIA! 🙌🏼",
                "¡MARCA LA DIFERENCIA! 😉",
                "¡TEN UNA IDENTIDAD ÚNICA! 😉"
            ]
        ];

        $message = [
            // Desarrollo y Diseño
            [
                "En <b>DigiMedia</b> diseñamos y desarrollamos sitios web modernos, rápidos y visualmente atractivos, pensados para que tu negocio destaque desde el <b>PRIMER CLICK</b>.<br><br>
                Lo que obtendrás con nosotros:<br><br>
                🌐 Diseños que enamoran a primera vista.<br>
                ⚡ Experiencias de usuario ágiles y memorables.<br>
                📈 Resultados que impulsan ventas y fidelizan clientes",

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

                "¿Tus redes sociales no generan interacciones? En DigiMedia, estamos comprometidos en potenciar tu presencia en línea a través de la gestión de redes sociales. Al confiarnos la administración de tus plataformas digitales, experimentarás un aumento significativo en la visibilidad y participación de tu marca. Nuestros beneficios:\n\n

                🚀 Potenciación de tu presencia digital.\n
                🚀 Contenido estratégico y de valor.\n\n

                Transformemos juntos tu presencia digital! ¡Háznoslo saber!",

                "¿Quieres tener contenido de calidad? Deja la gestión de tus redes sociales en manos expertas con DigiMedia y haz crecer tu negocio de la mejor manera con nuestros beneficios: \n\n

                - Planificación y organización de contenidos.\n
                - Análisis de resultados con informes mensuales.",

                "¿Buscas contenido de alto impacto? Confía en los especialistas de DigiMedia Marketing para gestionar tus redes sociales y lleva tu negocio al siguiente nivel con nuestro servicio de Gestión Redes Sociales.\n\n

                ✅ Diseño estratégico y calendario de contenido en redes.
                ✅ Análisis de desempeño con reportes mensuales y más!"
            ],

            [
                "En DigiMedia Marketing, estamos comprometidos en el mejor desarrollo en marketing digital. Tendremos el placer de armar estrategias que promuevan tu marca a través de diferentes entornos digitales. ¿Las estrategias que planteas no logran los objetivos de tu empresa?, entonces adquiere nuestro servicio con beneficios exclusivos: \n\n

                📌 Mejorar tu visibilidad online. \n
                📌 Vínculo de lealtad con los clientes. \n\n

                ¡No te quedes atrás en la era digital y transforma tu marca con soluciones innovadoras! Contacto y que comience tu presencia digital.",

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
                "¿Sientes que tu negocio no se diferencia del resto? ¡Haz que tu marca sea inolvidable!
                En DigiMedia, estamos preparados para llevar la identidad de tu marca a otro nivel. Somos especialistas en crear diseños irresistibles y branding cautivador.\n
                Adquiere nuestros beneficios exclusivos\n\n

                📌 Diferenciación y Reconocimiento\n

                Prepárate para darle un giro a tu negocio con todos nuestros beneficios ¡Contacte con nosotros!",

                "¿Quieres destacar entre tu competencia? Con DigiMedia podrás tener una marca sólida gracias a nuestro servicio de Branding y diseño que te ayudarán a ser reconocida entre tus clientes 🚀.\n\n

                - ✅ Desarrollo en identidad visual de tu marca\n
                - ✅ Originalidad en conceptos de marca ",

                "En DigiMedia garantizamos crear experiencias visuales impactantes y memorables para que puedas conectar con tu audiencia, nuestro servicio de Branding y diseño te ayudarán a lograr esto 🚀. Nuestros beneficios:\n\n
                - Originalidad en conceptos de marca\n
                - Construcción de confianza y credibilidad\n\n

                ¡Sé parte del mundo digital y potencia tu marca con nosotros! 🙌🏼"
            ]
        ];

        $extra_message = [
            // Desarrollo y Diseño
            [
                "¿Deseas potenciar tu marca en el mundo digital? Ven y hazlo con nuestros beneficios exclusivos. Marca la diferencia con una página web personalizada.<br>
                Saludos cordiales,<br>
                El equipo de Digimedia",
                "¿Quieres destacar en el mundo digital y atraer más clientes? No te quedes atrás en la era digital.<br>
                Contáctanos hoy y haz despegar tu marca.<br>
                Saludos cordiales,<br>
                El equipo de Digimedia",
                "¿Quieres multiplicar tus ventas y destacar en el mundo digital? El momento de crecer es ahora. Contáctanos y haz que tu marca conquiste el mundo digital.<br>
                Saludos cordiales,<br>
                El equipo de Digimedia"
            ]
        ];

        $head_title = [
            // Desarrollo y Diseño
            [
                "¿LISTO PARA POTENCIAR TU PÁGINA WEB?",
                "¿QUIERES MÁS VISIBILIDAD Y CLIENTES ONLINE?<br>
                ¡EMPIEZA CON DIGIMEDIA!",
                "HAZ QUE TE ENCUENTREN EN INTERNET<br>
                CONVIERTE TUS VISITAS EN VENTAS REALES"
            ]
        ];

        $message_send = $message[$this->id_service - 1][$this->number_message - 1];
        $image_send = $images[$this->id_service - 1][$this->number_message - 1];
        $title_send = $title[$this->id_service - 1][$this->number_message - 1];
        $subject_send = $subject[$this->id_service - 1][$this->number_message - 1];
        $extra_message_send = $extra_message[$this->id_service - 1][$this->number_message - 1] ?? null;
        $head_title_send = $head_title[$this->id_service - 1][$this->number_message - 1] ?? null;

        return $this->subject($subject_send)
                ->view('mails.modal')
                ->with([
                    'data' => $this->data,
                    'send_message' => $message_send,
                    'title' => $title_send,
                    'image' => $image_send,
                    'extra_message' => $extra_message_send,
                    'head_title' => $head_title_send,
                    'id_service' => $this->id_service,
                ]);
    }
}
