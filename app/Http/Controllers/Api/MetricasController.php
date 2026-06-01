<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Metricas\CardMetricDTO;
use App\DTOs\Metricas\EmpleadoMetricDTO;
use App\DTOs\Metricas\MonthYearDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Metricas\ListCardsByEmpleadoRequest;
use App\Http\Requests\Metricas\ListCardsByPlantillaRequest;
use App\Http\Requests\Metricas\MonthYearRequest;
use App\Http\Resources\CardResource;
use App\Services\MetricasService;
use Illuminate\Http\JsonResponse;

class MetricasController extends Controller
{
    public function __construct(
        private MetricasService $metricasService
    ) {}

    /* ============================================================
     * 1. METRICAS BLOGS
     * ============================================================
     */

    // 1.1 Cantidad de blogs creados por mes y año
    public function countBlogsByMonth(MonthYearRequest $request): JsonResponse
    {
        $dto = MonthYearDTO::fromRequest($request);
        $data = $this->metricasService->getBlogsCountByMonth($dto);

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    // 1.2 Blogs creados últimos 12 meses
    public function listBlogsByMonths12(): JsonResponse
    {
        $data = $this->metricasService->getBlogsLast12Months();

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    // 1.3 Top 5 meses con más blogs
    public function top5MothsWithMoreBlogs(): JsonResponse
    {
        $data = $this->metricasService->getTop5MonthsWithMoreBlogs();

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    /* ============================================================
     * 2. METRICAS POR PLANTILLA
     * ============================================================
     */

    // 2.1 Listar cards por plantilla
    public function listOfCardsByPlantilla(ListCardsByPlantillaRequest $request): JsonResponse
    {
        $dto = CardMetricDTO::fromRequest($request);
        $hasDateFilter = $request->has(['month', 'year']);

        $cards = $this->metricasService->getCardsByPlantilla($dto, $hasDateFilter);

        return response()->json([
            "status" => 200,
            "data" => CardResource::collection($cards)
        ]);
    }

    // 2.2 Cantidad de cards por plantilla
    public function countListOfCardsByPlantilla(ListCardsByPlantillaRequest $request): JsonResponse
    {
        $dto = CardMetricDTO::fromRequest($request);
        $count = $this->metricasService->countCardsByPlantilla($dto);

        return response()->json([
            "status" => 200,
            "count" => $count
        ]);
    }

    // 2.3 Tabla cards por plantilla
    public function tableCardsByIdPlantilla(MonthYearRequest $request): JsonResponse
    {
        $dto = MonthYearDTO::fromRequest($request);
        $data = $this->metricasService->getTableCardsByIdPlantilla($dto);

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    /* ============================================================
     * 3. METRICAS POR EMPLEADO
     * ============================================================
     */

    // 3.1 Cards por empleado
    public function listEmpleadoWithCards(ListCardsByEmpleadoRequest $request): JsonResponse
    {
        $dto = EmpleadoMetricDTO::fromRequest($request);
        $cards = $this->metricasService->getEmpleadoCards($dto);

        return response()->json([
            "status" => 200,
            "data" => CardResource::collection($cards)
        ]);
    }

    // 3.2 Cantidad cards por empleado
    public function countListOfCardsByEmpleado(ListCardsByEmpleadoRequest $request): JsonResponse
    {
        $dto = EmpleadoMetricDTO::fromRequest($request);
        $count = $this->metricasService->countEmpleadoCards($dto);

        return response()->json([
            "status" => 200,
            "count" => $count
        ]);
    }

    // 3.3 Tabla cards por empleado
    public function tableCardsByEmpleado(MonthYearRequest $request): JsonResponse
    {
        $dto = MonthYearDTO::fromRequest($request);
        $data = $this->metricasService->getTableCardsByEmpleado($dto);

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    /* ============================================================
     * 4. TIEMPO CREACIÓN → EDICIÓN
     * ============================================================
     */

    // 4.1 TIEMPO CREACIÓN → EDICIÓN
    public function tiempoCreacionEdicionPublicacionCard(MonthYearRequest $request): JsonResponse
    {
        $dto = MonthYearDTO::fromRequest($request);
        $data = $this->metricasService->getTiempoCreacionEdicion($dto);

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    // 4.2 Frecuencia de publicación de cards todos los empleados
    public function frecuenciaPublicacionCardsTodosEmpleados(): JsonResponse
    {
        try {
            $data = $this->metricasService->getFrecuenciaPublicacionTodosEmpleados();

            return response()->json([
                "status" => 200,
                "data"   => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
