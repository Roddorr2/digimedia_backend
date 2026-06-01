<?php

namespace App\Services;

use App\Repositories\ModalServicioRepository;
use App\Repositories\EmailModalRepository;
use App\Repositories\WatModalRepository;
use App\DTOs\ModalServicio\CreateModalServicioDTO;
use App\DTOs\ModalServicio\UpdateModalServicioDTO;
use App\Models\modalservicios;
use App\Jobs\SendCustomEmailJob;
use App\Jobs\SendWhatsAppJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModalServicioService
{
    public function __construct(
        private ModalServicioRepository $repository,
        private EmailModalRepository $emailModalRepository,
        private WatModalRepository $watModalRepository
    ) {}

    public function getModales(?string $search = null)
    {
        return $this->repository->getPaginated($search ?? '', 15);
    }

    public function getSendModales(int $id): array
    {
        $modal = $this->repository->findById($id);

        if (!$modal) {
            throw new ModelNotFoundException('Modal no encontrado');
        }

        $emails = $this->emailModalRepository->getByModalServicio($id);
        $whatsapps = $this->watModalRepository->getByModalServicio($id);

        return [
            'mails' => $emails,
            'wats' => $whatsapps
        ];
    }

    public function createModal(CreateModalServicioDTO $dto): modalservicios
    {
        DB::beginTransaction();
        try {
            $modalServicio = $this->repository->create($dto->toArray());

            $firstEmailModal = null;
            for ($i = 1; $i <= 3; $i++) {
                $emailModal = $this->emailModalRepository->create([
                    'estado' => 0,
                    'error' => '',
                    'id_modalservicio' => $modalServicio->id_modalservicio,
                    'number_message' => $i,
                    'fecha' => now(),
                ]);

                if ($i === 1) {
                    $firstEmailModal = $emailModal;
                }
            }

            for ($i = 1; $i <= 3; $i++) {
                $this->watModalRepository->create([
                    'estado' => 0,
                    'error' => '',
                    'id_modalservicio' => $modalServicio->id_modalservicio,
                    'number_message' => $i,
                    'fecha' => now(),
                ]);
            }

            try {
                $data = [
                    'nombre' => $dto->nombre,
                    'correo' => $dto->correo,
                    'telefono' => $dto->telefono
                ];

                // AQUI SE ENVÍA EL PRIMER CORREO (inmediato)
                dispatch(new SendCustomEmailJob($dto->correo, $data, $dto->id_servicio, 1));

                // AQUI SE ENVÍA EL SEGUNDO CORREO (+30 minutos después)
                dispatch(new SendCustomEmailJob($dto->correo, $data, $dto->id_servicio, 2))
                    ->delay(now()->addMinutes(30));

                // AQUI SE ENVÍA EL TERCER CORREO (+1 hora después)
                dispatch(new SendCustomEmailJob($dto->correo, $data, $dto->id_servicio, 3))
                    ->delay(now()->addHours(1));

                // ------- AQUI SE ENVIAN MENSAJES WHATSAPP -------
                $whatsapps = $this->watModalRepository->getByModalServicio($modalServicio->id_modalservicio);

                $wat1 = $whatsapps->firstWhere('number_message', 1);
                if ($wat1) {
                    dispatch(new SendWhatsAppJob($wat1, $data, $dto->id_servicio));
                }

                $wat2 = $whatsapps->firstWhere('number_message', 2);
                if ($wat2) {
                    dispatch(new SendWhatsAppJob($wat2, $data, $dto->id_servicio))
                        ->delay(now()->addMinutes(30));
                }

                $wat3 = $whatsapps->firstWhere('number_message', 3);
                if ($wat3) {
                    dispatch(new SendWhatsAppJob($wat3, $data, $dto->id_servicio))
                        ->delay(now()->addHours(1));
                }

                if ($firstEmailModal) {
                    $this->emailModalRepository->update($firstEmailModal, [
                        'estado' => 1,
                        'fecha' => now(),
                    ]);
                }

            } catch (\Exception $e) {
                Log::error("Error enviando primer correo/whatsapp en funnel", [
                    'error' => $e->getMessage()
                ]);

                if ($firstEmailModal) {
                    $this->emailModalRepository->update($firstEmailModal, [
                        'estado' => 1,
                        'error' => 'Enviado con error, Posiblemente el correo no existe',
                        'fecha' => now(),
                    ]);
                }
            }

            DB::commit();
            return $modalServicio;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getModalById(int $id): modalservicios
    {
        $modal = $this->repository->findByIdWithServicio($id);

        if (!$modal) {
            throw new ModelNotFoundException('Modal no encontrado');
        }

        return $modal;
    }

    public function updateModal(int $id, UpdateModalServicioDTO $dto): modalservicios
    {
        $modal = $this->repository->findById($id);

        if (!$modal) {
            throw new ModelNotFoundException('Modal no encontrado');
        }

        $this->repository->update($modal, [
            'estado' => $dto->estado,
        ]);

        return $modal->fresh();
    }

    public function deleteModal(int $id): void
    {
        $modal = $this->repository->findById($id);

        if (!$modal) {
            throw new ModelNotFoundException('Modal no encontrado');
        }

        DB::beginTransaction();
        try {
            $this->emailModalRepository->deleteByModalServicio($id);
            $this->watModalRepository->deleteByModalServicio($id);
            $this->repository->delete($modal);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
