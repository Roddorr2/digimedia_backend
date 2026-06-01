<?php

namespace App\Services;

use App\Repositories\WatModalRepository;
use App\Repositories\ModalServicioRepository;
use App\DTOs\ModalWat\ChangeWatEstadoDTO;
use App\Models\WatModal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModalWhatsAppService
{
    private const MESSAGES_MAP = [
        [
            '👋 ¡Hola! Nos comunicamos contigo para informarte que hemos recibido tu mensaje sobre nuestro Servicio de Diseño y Desarrollo Web. ✅ En breve uno de nuestros especialistas se pondrá en contacto contigo. - Atentamente, el equipo de DigiMedia.',
            '🚀 Gracias por confiar en DigiMedia. Tu solicitud sobre Diseño y Desarrollo Web fue recibida con éxito. 💡 ¡Nos encantará ayudarte a impulsar tus ideas!'
        ],
        [
            '👋 ¡Hola! Hemos recibido tu consulta sobre nuestro Servicio de Gestión de Redes Sociales. ✅ Nuestro equipo la está revisando y se pondrá en contacto contigo pronto. - El equipo de DigiMedia.',
            '💡 Recibimos tu mensaje sobre Gestión de Redes Sociales. Estamos ansiosos por ayudarte a mejorar tu presencia digital. 📢 ¡Nos comunicaremos contigo enseguida!'
        ],
        [
            '👋 ¡Hola! Confirmamos que recibimos tu mensaje sobre nuestro Servicio de Marketing y Gestión Digital. ✅ En breve un asesor te contactará. - Atentamente, DigiMedia.',
            '🎯 Tu solicitud sobre Marketing y Gestión Digital ya está en nuestro sistema. Gracias por preferirnos, pronto te brindaremos más información. 🚀'
        ],
        [
            '👋 ¡Hola! Tu mensaje sobre nuestro Servicio de Branding y Diseño Gráfico fue recibido correctamente. ✅ Pronto uno de nuestros diseñadores te contactará. - DigiMedia.',
            '🎨 Gracias por escribirnos acerca de Branding y Diseño Gráfico. 💡 Estamos listos para ayudarte a construir una marca memorable. ¡Hablamos pronto! 🚀'
        ]
    ];

    public function __construct(
        private WatModalRepository $repository,
        private ModalServicioRepository $modalServicioRepository
    ) {}

    public function generateWhatsAppUrl(int $id): string
    {
        $modalWat = $this->repository->findById($id);

        if (!$modalWat) {
            throw new ModelNotFoundException('Mensaje no encontrado');
        }

        $modal = $this->modalServicioRepository->findById($modalWat->id_modalservicio);
        if (!$modal) {
            throw new ModelNotFoundException('Lead asociado no encontrado');
        }

        $telefono = $modal->telefono;
        $servicioIdx = $modal->id_servicio - 1;
        $mensajeIdx = $modalWat->number_message - 1;

        if (!isset(self::MESSAGES_MAP[$servicioIdx][$mensajeIdx])) {
            throw new \InvalidArgumentException('Configuración de mensaje inválida');
        }

        $mensaje = urlencode(self::MESSAGES_MAP[$servicioIdx][$mensajeIdx]);

        return "https://wa.me/51$telefono?text=$mensaje";
    }

    public function cambiarEstado(int $id, ChangeWatEstadoDTO $dto): WatModal
    {
        $modalWat = $this->repository->findById($id);

        if (!$modalWat) {
            throw new ModelNotFoundException('Mensaje no encontrado');
        }

        if ($dto->estado === 1) {
            $this->repository->update($modalWat, [
                'estado' => 1,
                'fecha' => now(),
            ]);
        } else {
            $this->repository->update($modalWat, [
                'estado' => 0,
                'error' => $dto->error,
                'fecha' => now(),
            ]);
        }

        return $modalWat->fresh();
    }
}
