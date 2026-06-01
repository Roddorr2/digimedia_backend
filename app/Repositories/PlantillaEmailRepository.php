<?php

namespace App\Repositories;

use App\Models\PlantillaEmail;
use Illuminate\Support\Collection;

class PlantillaEmailRepository
{
    public function getAll(): Collection
    {
        return PlantillaEmail::with('servicio')
            ->orderBy('id_servicio')
            ->orderBy('numero_plantilla')
            ->get();
    }

    public function findById(int $id): ?PlantillaEmail
    {
        return PlantillaEmail::with('servicio')->find($id);
    }

    public function findByServicioNumero(int $idServicio, int $numeroPlantilla): ?PlantillaEmail
    {
        return PlantillaEmail::where('id_servicio', $idServicio)
            ->where('numero_plantilla', $numeroPlantilla)
            ->with('servicio')
            ->first();
    }

    public function update(PlantillaEmail $plantilla, array $data): bool
    {
        return $plantilla->update($data);
    }
}
