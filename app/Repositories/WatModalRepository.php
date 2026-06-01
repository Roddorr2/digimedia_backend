<?php

namespace App\Repositories;

use App\Models\WatModal;

class WatModalRepository
{
    public function findByCampaniaAndLead(int $campaniaId, int $watModalId): ?WatModal
    {
        return WatModal::where('id_modal_wat', $watModalId)
            ->where('campania_id', $campaniaId)
            ->first();
    }

    public function update(WatModal $watModal, array $data): bool
    {
        return $watModal->update($data);
    }

    public function create(array $data): WatModal
    {
        return WatModal::create($data);
    }

    public function findById(int $id): ?WatModal
    {
        return WatModal::find($id);
    }

    public function getByModalServicio(int $idModalservicio): \Illuminate\Support\Collection
    {
        return WatModal::where('id_modalservicio', $idModalservicio)->get();
    }

    public function deleteByModalServicio(int $idModalservicio): void
    {
        WatModal::where('id_modalservicio', $idModalservicio)->delete();
    }
}