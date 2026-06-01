<?php

namespace App\Repositories;

use App\Models\Subservicio;
use Illuminate\Support\Collection;

class SubservicioRepository
{
    public function getAll(): Collection
    {
        return Subservicio::with('servicio')
            ->orderBy('id_servicio')
            ->orderBy('id_subservicio')
            ->get();
    }

    public function getByServicio(int $idServicio): Collection
    {
        return Subservicio::with('servicio')
            ->byServicio($idServicio)
            ->orderBy('id_subservicio')
            ->get();
    }
}
