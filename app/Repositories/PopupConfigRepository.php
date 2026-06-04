<?php

namespace App\Repositories;

use App\Models\PopupConfig;
use Illuminate\Support\Collection;

class PopupConfigRepository
{
    public function getAll(): Collection
    {
        return PopupConfig::with(['popupable', 'createdBy:id,name', 'updatedBy:id,name'])->get();
    }

    public function findById(int $id): ?PopupConfig
    {
        return PopupConfig::with(['popupable', 'createdBy:id,name', 'updatedBy:id,name'])->find($id);
    }

    public function findByOwner(string $ownerType, int $ownerId): ?PopupConfig
    {
        return PopupConfig::where('popupable_type', $ownerType)
            ->where('popupable_id', $ownerId)
            ->with(['popupable', 'createdBy:id,name', 'updatedBy:id,name'])
            ->first();
    }

    public function existsForOwner(string $ownerType, int $ownerId): bool
    {
        return PopupConfig::where('popupable_type', $ownerType)
            ->where('popupable_id', $ownerId)
            ->exists();
    }

    public function create(array $data): PopupConfig
    {
        return PopupConfig::create($data);
    }

    public function update(PopupConfig $popup, array $data): bool
    {
        return $popup->update($data);
    }

    public function delete(PopupConfig $popup): bool
    {
        return $popup->delete();
    }
}
