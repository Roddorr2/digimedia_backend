<?php

namespace App\Services;

use App\Repositories\EmailModalRepository;
use App\Repositories\ModalServicioRepository;
use App\DTOs\ModalMail\ReportMailErrorDTO;
use App\Models\EmailModal;
use App\Mail\MailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModalEmailService
{
    public function __construct(
        private EmailModalRepository $repository,
        private ModalServicioRepository $modalServicioRepository
    ) {}

    public function sendMail(int $id): EmailModal
    {
        $modalMail = $this->repository->findById($id);

        if (!$modalMail) {
            throw new ModelNotFoundException('Mensaje no encontrado');
        }

        $modal = $this->modalServicioRepository->findById($modalMail->id_modalservicio);
        if (!$modal) {
            throw new ModelNotFoundException('Lead asociado no encontrado');
        }

        try {
            $data = [
                'nombre' => $modal->nombre,
                'telefono' => $modal->telefono,
                'correo' => $modal->correo,
            ];

            Mail::to($modal->correo)->send(
                new MailService($modalMail->number_message, $data, $modal->id_servicio)
            );

            $this->repository->update($modalMail, [
                'estado' => 1,
                'fecha' => now(),
            ]);

            return $modalMail->fresh();

        } catch (\Exception $e) {
            $this->repository->update($modalMail, [
                'estado' => 1,
                'error' => 'Enviado con error, el correo no existe',
                'fecha' => now(),
            ]);
            throw new \Exception('Error al enviar el correo: ' . $e->getMessage(), 500);
        }
    }

    public function reportarError(int $id, ReportMailErrorDTO $dto): EmailModal
    {
        $modalMail = $this->repository->findById($id);
        if (!$modalMail) {
            throw new ModelNotFoundException('Mensaje no encontrado');
        }

        $this->repository->update($modalMail, [
            'estado' => 1,
            'error' => $dto->error,
            'fecha' => now(),
        ]);

        return $modalMail->fresh();
    }
}
