<?php

namespace App\Repositories;

use App\Models\ConfiguracionTiempo;
use Illuminate\Support\Collection;

class ConfiguracionTiempoRepository
{
    public function getByServicio(int $idServicio): Collection
    {
        return ConfiguracionTiempo::where('id_servicio', $idServicio)
            ->orderBy('tipo')
            ->orderBy('numero_mensaje')
            ->get();
    }

    public function deleteTiemposNotIn(int $idServicio, string $tipo, array $numeros): void
    {
        ConfiguracionTiempo::where('id_servicio', $idServicio)
            ->where('tipo', $tipo)
            ->whereNotIn('numero_mensaje', $numeros)
            ->delete();
    }

    public function updateOrCreate(array $attributes, array $values): ConfiguracionTiempo
    {
        return ConfiguracionTiempo::updateOrCreate($attributes, $values);
    }

    public function deleteSpecific(int $idServicio, string $tipo, int $numeroMensaje): bool
    {
        $deleted = ConfiguracionTiempo::where('id_servicio', $idServicio)
            ->where('tipo', $tipo)
            ->where('numero_mensaje', $numeroMensaje)
            ->delete();

        return (bool) $deleted;
    }
}
