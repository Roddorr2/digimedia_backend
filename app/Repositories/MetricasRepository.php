<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Models\Card;
use App\Models\Empleado;
use App\Models\BlogAuditoria;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MetricasRepository
{
    public function countBlogsInMonth(Carbon $start, Carbon $end): int
    {
        return BlogAuditoria::where('accion', 'CREAR')
            ->whereBetween('fecha_hora', [$start, $end])
            ->count();
    }

    public function getBlogsInDateRange(Carbon $start, Carbon $end): Collection
    {
        return BlogAuditoria::where('accion', 'CREAR')
            ->whereBetween('fecha_hora', [$start, $end])
            ->get();
    }

    public function getAllBlogsWithCreateAuditoria(): Collection
    {
        return BlogAuditoria::where('accion', 'CREAR')->get();
    }

    public function getCardsByPlantilla(int $idPlantilla, ?Carbon $start, ?Carbon $end, bool $hasDateFilter): Collection
    {
        $query = Card::with(['blog.head', 'empleado'])
            ->leftJoin('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_plantilla', $idPlantilla)
            ->select('cards.*');

        $query->where(function ($q) {
            $q->where('ba.accion', 'CREAR')
                ->orWhereNull('ba.accion');
        });

        if ($hasDateFilter && $start && $end) {
            $query->whereBetween('ba.fecha_hora', [$start, $end]);
        }

        return $query->get();
    }

    public function countCardsByPlantillaInMonth(int $idPlantilla, Carbon $start, Carbon $end): int
    {
        return Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_plantilla', $idPlantilla)
            ->where('ba.accion', 'CREAR')
            ->whereBetween('ba.fecha_hora', [$start, $end])
            ->count();
    }

    public function getTableCardsByPlantilla(Carbon $start, Carbon $end): Collection
    {
        return Card::query()
            ->selectRaw('cards.id_plantilla, COUNT(cards.id_card) as count_cards')
            ->join('blog_auditoria as ba', function ($join) use ($start, $end) {
                $join->on('ba.id_blog', '=', 'cards.id_blog')
                    ->where('ba.accion', '=', 'CREAR')
                    ->whereBetween('ba.fecha_hora', [$start, $end]);
            })
            ->groupBy('cards.id_plantilla')
            ->orderBy('cards.id_plantilla')
            ->get();
    }

    public function getCardsByEmpleado(int $idEmpleado, Carbon $start, Carbon $end): Collection
    {
        return Card::with(['blog.head', 'empleado'])
            ->join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_empleado', $idEmpleado)
            ->where('ba.accion', 'CREAR')
            ->whereBetween('ba.fecha_hora', [$start, $end])
            ->select('cards.*')
            ->get();
    }

    public function countCardsByEmpleadoInMonth(int $idEmpleado, Carbon $start, Carbon $end): int
    {
        return Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_empleado', $idEmpleado)
            ->where('ba.accion', 'CREAR')
            ->whereBetween('ba.fecha_hora', [$start, $end])
            ->count();
    }

    public function getTableCardsByEmpleado(Carbon $start, Carbon $end): Collection
    {
        return Empleado::query()
            ->selectRaw('empleados.id_empleado, empleados.nombre as nombre_empleado, COUNT(DISTINCT CASE WHEN ba.id_blog IS NOT NULL THEN cards.id_card END) as count_cards')
            ->leftJoin('cards', 'cards.id_empleado', '=', 'empleados.id_empleado')
            ->leftJoin('blog_auditoria as ba', function ($join) use ($start, $end) {
                $join->on('ba.id_blog', '=', 'cards.id_blog')
                    ->where('ba.accion', '=', 'CREAR')
                    ->whereBetween('ba.fecha_hora', [$start, $end]);
            })
            ->where('empleados.id_rol', 1)
            ->groupBy('empleados.id_empleado', 'empleados.nombre')
            ->get();
    }

    public function getAuditoriasByActionsAndDates(array $acciones, Carbon $start, Carbon $end): Collection
    {
        return BlogAuditoria::whereIn('accion', $acciones)
            ->whereBetween('fecha_hora', [$start, $end])
            ->orderBy('id_blog')
            ->get();
    }

    public function getEmpleadosWithCardsCount(): Collection
    {
        return Empleado::where('id_rol', 1)
            ->withCount('cards')
            ->get();
    }
}
