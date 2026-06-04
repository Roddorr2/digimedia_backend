<?php

namespace App\Services;

use App\Repositories\CampaniaRepository;
use App\DTOs\Campania\CampaniaFiltersDTO;
use App\Models\CampaniaWhatsApp;

class CampaniaService
{
    public function __construct(
        private CampaniaRepository $campaniaRepository
    ) {}

    public function getCampaniasList(CampaniaFiltersDTO $filters): array
    {
        $campanias = $this->campaniaRepository->getPaginatedWithFilters($filters);
        $activeCampaign = $this->campaniaRepository->getActiveCampaign();

        return [
            'campanias' => $campanias,
            'active_campaign' => $activeCampaign
        ];
    }

    public function getCampaniaDetails(int $id): CampaniaWhatsApp
    {
        $campania = $this->campaniaRepository->findById($id);
        
        if (!$campania) {
            throw new \Exception('Campaña no encontrada', 404);
        }
        
        return $campania;
    }
}