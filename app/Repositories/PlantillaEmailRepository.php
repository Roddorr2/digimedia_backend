<?php

namespace App\Repositories;

use App\Models\PlantillaEmail;
use App\Models\servicios;
use App\Models\Subservicio;
use Illuminate\Support\Collection;

class PlantillaEmailRepository
{
    public function getAll(): Collection
    {
        return PlantillaEmail::with('plantillable')
            ->orderBy('plantillable_type')
            ->orderBy('plantillable_id')
            ->orderBy('numero_plantilla')
            ->get();
    }

    public function findById(int $id): ?PlantillaEmail
    {
        return PlantillaEmail::with('plantillable')->find($id);
    }

    public function getByOwner(string $ownerType, int $ownerId): Collection
    {
        return PlantillaEmail::with('plantillable')
            ->where('plantillable_type', $ownerType)
            ->where('plantillable_id', $ownerId)
            ->orderBy('numero_plantilla')
            ->get();
    }

    public function findByOwnerNumero(string $ownerType, int $ownerId, int $numero): ?PlantillaEmail
    {
        return PlantillaEmail::with('plantillable')
            ->where('plantillable_type', $ownerType)
            ->where('plantillable_id', $ownerId)
            ->where('numero_plantilla', $numero)
            ->first();
    }

    public function findByServicioNumero(int $idServicio, int $numeroPlantilla): ?PlantillaEmail
    {
        return $this->findByOwnerNumero(servicios::class, $idServicio, $numeroPlantilla);
    }

    public function createForOwner(array $data): PlantillaEmail
    {
        return PlantillaEmail::create($data);
    }

    public function update(PlantillaEmail $plantilla, array $data): bool
    {
        return $plantilla->update($data);
    }
}
