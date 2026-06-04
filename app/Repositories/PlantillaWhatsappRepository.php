<?php

namespace App\Repositories;

use App\Models\PlantillaWhatsapp;
use Illuminate\Support\Collection;

class PlantillaWhatsappRepository
{
    public function getAll(): Collection
    {
        return PlantillaWhatsapp::with('servicio')
            ->orderBy('id_servicio')
            ->orderBy('numero_plantilla')
            ->get();
    }

    public function findById(int $id): ?PlantillaWhatsapp
    {
        return PlantillaWhatsapp::with('servicio')->find($id);
    }

    public function findByServicioNumero(int $idServicio, int $numeroPlantilla): ?PlantillaWhatsapp
    {
        return PlantillaWhatsapp::where('id_servicio', $idServicio)
            ->where('numero_plantilla', $numeroPlantilla)
            ->with('servicio')
            ->first();
    }

    public function update(PlantillaWhatsapp $plantilla, array $data): bool
    {
        return $plantilla->update($data);
    }
}
