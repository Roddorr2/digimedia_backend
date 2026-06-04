<?php

namespace App\Services;

use App\Repositories\MetricasRepository;
use App\DTOs\Metricas\MonthYearDTO;
use App\DTOs\Metricas\CardMetricDTO;
use App\DTOs\Metricas\EmpleadoMetricDTO;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MetricasService
{
    public function __construct(
        private MetricasRepository $repository
    ) {}

    public function getBlogsCountByMonth(MonthYearDTO $dto): array
    {
        $startDate = Carbon::createFromDate($dto->year, $dto->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dto->year, $dto->month, 1)->endOfMonth();

        $count = $this->repository->countBlogsInMonth($startDate, $endDate);

        return [
            "month" => $dto->month,
            "year" => $dto->year,
            "total_blogs" => $count
        ];
    }

    public function getBlogsLast12Months(): array
    {
        $endDate = Carbon::now()->endOfMonth();
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $blogs = $this->repository->getBlogsInDateRange($startDate, $endDate);

        $raw = $blogs->groupBy(fn($i) => Carbon::parse($i->fecha_hora)->format('Y-m'))
            ->map(fn($group) => [
                'y' => (int) Carbon::parse($group->first()->fecha_hora)->format('Y'),
                'm' => (int) Carbon::parse($group->first()->fecha_hora)->format('m'),
                'total' => $group->count()
            ])
            ->keyBy(fn($i) => $i['y'] . '-' . str_pad($i['m'], 2, '0', STR_PAD_LEFT));

        $data = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $key = $date->format('Y-m');

            $data[] = [
                "month" => $date->format('F Y'),
                "total_blogs" => isset($raw[$key]) ? $raw[$key]['total'] : 0
            ];
        }

        return $data;
    }

    public function getTop5MonthsWithMoreBlogs(): array
    {
        $blogs = $this->repository->getAllBlogsWithCreateAuditoria();

        return $blogs->groupBy(fn($i) => Carbon::parse($i->fecha_hora)->format('Y-m'))
            ->map(fn($group) => [
                'y' => (int) Carbon::parse($group->first()->fecha_hora)->format('Y'),
                'm' => (int) Carbon::parse($group->first()->fecha_hora)->format('m'),
                'total' => $group->count()
            ])
            ->sortByDesc('total')
            ->take(5)
            ->map(fn($i) => [
                "month" => Carbon::create($i['y'], $i['m'])->format('F Y'),
                "total_blogs" => $i['total']
            ])
            ->values()
            ->toArray();
    }

    public function getCardsByPlantilla(CardMetricDTO $dto, bool $hasDateFilter): Collection
    {
        $startDate = Carbon::createFromDate($dto->dateDto->year, $dto->dateDto->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dto->dateDto->year, $dto->dateDto->month, 1)->endOfMonth();

        return $this->repository->getCardsByPlantilla($dto->id_plantilla, $startDate, $endDate, $hasDateFilter);
    }

    public function countCardsByPlantilla(CardMetricDTO $dto): int
    {
        $startDate = Carbon::createFromDate($dto->dateDto->year, $dto->dateDto->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dto->dateDto->year, $dto->dateDto->month, 1)->endOfMonth();

        return $this->repository->countCardsByPlantillaInMonth($dto->id_plantilla, $startDate, $endDate);
    }

    public function getTableCardsByIdPlantilla(MonthYearDTO $dto): Collection
    {
        $startDate = Carbon::createFromDate($dto->year, $dto->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dto->year, $dto->month, 1)->endOfMonth();

        return $this->repository->getTableCardsByPlantilla($startDate, $endDate);
    }

    public function getEmpleadoCards(EmpleadoMetricDTO $dto): Collection
    {
        $startDate = Carbon::createFromDate($dto->dateDto->year, $dto->dateDto->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dto->dateDto->year, $dto->dateDto->month, 1)->endOfMonth();

        return $this->repository->getCardsByEmpleado($dto->id_empleado, $startDate, $endDate);
    }

    public function countEmpleadoCards(EmpleadoMetricDTO $dto): int
    {
        $startDate = Carbon::createFromDate($dto->dateDto->year, $dto->dateDto->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dto->dateDto->year, $dto->dateDto->month, 1)->endOfMonth();

        return $this->repository->countCardsByEmpleadoInMonth($dto->id_empleado, $startDate, $endDate);
    }

    public function getTableCardsByEmpleado(MonthYearDTO $dto): Collection
    {
        $startDate = Carbon::createFromDate($dto->year, $dto->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dto->year, $dto->month, 1)->endOfMonth();

        return $this->repository->getTableCardsByEmpleado($startDate, $endDate);
    }

    public function getTiempoCreacionEdicion(MonthYearDTO $dto): array
    {
        $startDate = Carbon::createFromDate($dto->year, $dto->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dto->year, $dto->month, 1)->endOfMonth();

        $auditorias = $this->repository->getAuditoriasByActionsAndDates(['CREAR', 'ACTUALIZAR'], $startDate, $endDate);

        return $auditorias->groupBy('id_blog')
            ->map(function ($items, $id_blog) {
                $crear = $items->firstWhere('accion', 'CREAR');
                $editar = $items->firstWhere('accion', 'ACTUALIZAR');

                if (!$crear || !$editar) return null;

                return [
                    "id_blog" => $id_blog,
                    "tiempo_minutos" => Carbon::parse($crear->fecha_hora)
                        ->diffInMinutes(Carbon::parse($editar->fecha_hora))
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function getFrecuenciaPublicacionTodosEmpleados(): Collection
    {
        $now = now();
        $empleados = $this->repository->getEmpleadosWithCardsCount();

        return $empleados->map(function ($empleado) use ($now) {
            $mesesTrabajados = $empleado->created_at
                ? $empleado->created_at->diffInMonths($now) + 1
                : 1;

            return [
                'id_empleado' => $empleado->id_empleado,
                'nombre_empleado' => $empleado->nombre,
                'frecuencia_publicacion_mensual' => round($empleado->cards_count / $mesesTrabajados, 2),
            ];
        });
    }
}
