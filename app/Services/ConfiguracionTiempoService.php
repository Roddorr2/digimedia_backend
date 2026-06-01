<?php

namespace App\Services;

use App\Repositories\ConfiguracionTiempoRepository;
use App\DTOs\ConfiguracionTiempo\UpdateConfiguracionTiempoDTO;
use App\DTOs\ConfiguracionTiempo\StoreConfiguracionTiempoDTO;
use App\Models\ConfiguracionTiempo;
use Illuminate\Support\Facades\DB;

class ConfiguracionTiempoService
{
    public function __construct(
        private ConfiguracionTiempoRepository $repository
    ) {}

    public function getTiemposByServicio(int $idServicio): array
    {
        $configuraciones = $this->repository->getByServicio($idServicio);

        $email = $configuraciones->where('tipo', 'email')->values();
        $whatsapp = $configuraciones->where('tipo', 'whatsapp')->values();

        return [
            'email' => $email,
            'whatsapp' => $whatsapp
        ];
    }

    public function updateTiempos(int $idServicio, UpdateConfiguracionTiempoDTO $dto): void
    {
        DB::beginTransaction();
        try {
            $emailNumeros = collect($dto->email)->pluck('numero_mensaje')->toArray();
            $whatsappNumeros = collect($dto->whatsapp)->pluck('numero_mensaje')->toArray();

            $this->repository->deleteTiemposNotIn($idServicio, 'email', $emailNumeros);
            $this->repository->deleteTiemposNotIn($idServicio, 'whatsapp', $whatsappNumeros);

            foreach ($dto->email as $config) {
                $this->repository->updateOrCreate(
                    [
                        'id_servicio' => $idServicio,
                        'tipo' => 'email',
                        'numero_mensaje' => $config['numero_mensaje'],
                    ],
                    [
                        'unidad_tiempo' => $config['unidad_tiempo'],
                        'valor_tiempo' => $config['valor_tiempo'],
                    ]
                );
            }

            foreach ($dto->whatsapp as $config) {
                $this->repository->updateOrCreate(
                    [
                        'id_servicio' => $idServicio,
                        'tipo' => 'whatsapp',
                        'numero_mensaje' => $config['numero_mensaje'],
                    ],
                    [
                        'unidad_tiempo' => $config['unidad_tiempo'],
                        'valor_tiempo' => $config['valor_tiempo'],
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function storeTiempo(int $idServicio, StoreConfiguracionTiempoDTO $dto): ConfiguracionTiempo
    {
        return $this->repository->updateOrCreate(
            [
                'id_servicio' => $idServicio,
                'tipo' => $dto->tipo,
                'numero_mensaje' => $dto->numero_mensaje,
            ],
            [
                'unidad_tiempo' => $dto->unidad_tiempo,
                'valor_tiempo' => $dto->valor_tiempo,
            ]
        );
    }

    public function deleteTiempo(int $idServicio, string $tipo, int $numeroMensaje): bool
    {
        return $this->repository->deleteSpecific($idServicio, $tipo, $numeroMensaje);
    }
}
