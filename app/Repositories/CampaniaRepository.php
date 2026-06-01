<?php

namespace App\Repositories;

use App\Models\CampaniaWhatsApp;
use App\DTOs\Campania\CampaniaFiltersDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CampaniaRepository
{
    public function getPaginatedWithFilters(CampaniaFiltersDTO $filters): LengthAwarePaginator
    {
        $query = CampaniaWhatsApp::with('servicio');

        if ($filters->estado) {
            $query->where('estado', $filters->estado);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters->perPage);
    }

    public function findById(int $id): ?CampaniaWhatsApp
    {
        return CampaniaWhatsApp::with(['servicio', 'usuario', 'mensajesWhatsApp'])->find($id);
    }

    public function getActiveCampaign(): ?CampaniaWhatsApp
    {
        return CampaniaWhatsApp::getActiveCampaign();
    }

    public function getValidLeadsIdsForCampaign(CampaniaWhatsApp $campania): array
    {
        return \App\Models\modalservicios::query()
            ->where('id_servicio', $campania->id_servicio)
            ->where('estado', 1)
            ->whereNotNull('telefono')
            ->where('telefono', '!=', '')
            ->where('modalservicios.fecha', '<=', $campania->fecha_inicio)
            ->orderBy('id_modalservicio')
            ->get()
            ->unique('telefono')
            ->pluck('id_modalservicio')
            ->toArray();
    }
}