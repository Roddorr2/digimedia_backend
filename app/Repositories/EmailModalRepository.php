<?php

namespace App\Repositories;

use App\Models\EmailModal;
use Illuminate\Support\Collection;

class EmailModalRepository
{
    public function findById(int $id): ?EmailModal
    {
        return EmailModal::find($id);
    }

    public function getByModalServicio(int $idModalservicio): Collection
    {
        return EmailModal::where('id_modalservicio', $idModalservicio)->get();
    }

    public function create(array $data): EmailModal
    {
        return EmailModal::create($data);
    }

    public function update(EmailModal $model, array $data): bool
    {
        return $model->update($data);
    }

    public function deleteByModalServicio(int $idModalservicio): void
    {
        EmailModal::where('id_modalservicio', $idModalservicio)->delete();
    }
}
