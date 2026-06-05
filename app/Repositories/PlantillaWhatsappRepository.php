<?php

namespace App\Repositories;

use App\Models\PlantillaWhatsapp;
use App\Models\servicios;
use App\Models\Subservicio;
use Illuminate\Support\Collection;

class PlantillaWhatsappRepository
{
    public function getAll(): Collection
    {
        return PlantillaWhatsapp::with('plantillable')
            ->orderBy('plantillable_type')
            ->orderBy('plantillable_id')
            ->orderBy('numero_plantilla')
            ->get();
    }

    public function findById(int $id): ?PlantillaWhatsapp
    {
        return PlantillaWhatsapp::with('plantillable')->find($id);
    }

    public function getByOwner(string $ownerType, int $ownerId): Collection
    {
        return PlantillaWhatsapp::with('plantillable')
            ->where('plantillable_type', $ownerType)
            ->where('plantillable_id', $ownerId)
            ->orderBy('numero_plantilla')
            ->get();
    }

    public function findByOwnerNumero(string $ownerType, int $ownerId, int $numero): ?PlantillaWhatsapp
    {
        return PlantillaWhatsapp::with('plantillable')
            ->where('plantillable_type', $ownerType)
            ->where('plantillable_id', $ownerId)
            ->where('numero_plantilla', $numero)
            ->first();
    }

    public function findByServicioNumero(int $idServicio, int $numeroPlantilla): ?PlantillaWhatsapp
    {
        return $this->findByOwnerNumero(servicios::class, $idServicio, $numeroPlantilla);
    }

    public function createForOwner(array $data): PlantillaWhatsapp
    {
        return PlantillaWhatsapp::create($data);
    }

    public function update(PlantillaWhatsapp $plantilla, array $data): bool
    {
        return $plantilla->update($data);
    }
}
