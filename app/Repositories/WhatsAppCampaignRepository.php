<?php

namespace App\Repositories;

use App\Models\CampaniaWhatsApp;
use Illuminate\Support\Collection;

class WhatsAppCampaignRepository
{
    public function findById(int $id): ?CampaniaWhatsApp
    {
        return CampaniaWhatsApp::find($id);
    }

    public function findByIdWithServicio(int $id): ?CampaniaWhatsApp
    {
        return CampaniaWhatsApp::with('servicio')->find($id);
    }

    public function getActiveCampaign(): ?CampaniaWhatsApp
    {
        return CampaniaWhatsApp::getActiveCampaign();
    }

    public function create(array $data): CampaniaWhatsApp
    {
        return CampaniaWhatsApp::create($data);
    }

    public function update(CampaniaWhatsApp $campania, array $data): bool
    {
        return $campania->update($data);
    }

    public function listRecent(int $limit): Collection
    {
        return CampaniaWhatsApp::with('servicio')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
