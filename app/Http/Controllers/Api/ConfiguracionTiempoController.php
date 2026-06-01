<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfiguracionTiempo\UpdateConfiguracionTiempoRequest;
use App\Http\Requests\ConfiguracionTiempo\StoreConfiguracionTiempoRequest;
use App\Http\Resources\ConfiguracionTiempoResource;
use App\Services\ConfiguracionTiempoService;
use App\DTOs\ConfiguracionTiempo\UpdateConfiguracionTiempoDTO;
use App\DTOs\ConfiguracionTiempo\StoreConfiguracionTiempoDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConfiguracionTiempoController extends Controller
{
    public function __construct(
        private ConfiguracionTiempoService $configuracionTiempoService
    ) {}

    /*
    * GET /api/servicios/(id_servicio)/tiempos
    * Obtiene la configuración de tiempo para un servicio
    */
    public function get(int $id_servicio): JsonResponse
    {
        $result = $this->configuracionTiempoService->getTiemposByServicio($id_servicio);

        return response()->json([
            'status' => 200,
            'data' => [
                'email' => ConfiguracionTiempoResource::collection($result['email']),
                'whatsapp' => ConfiguracionTiempoResource::collection($result['whatsapp'])
            ]
        ], 200);
    }

    /**
     * PUT /api/servicios/{id_servicio}/tiempos
     * Reemplaza toda la configuración de tiempos de un servicio
     */
    public function update(UpdateConfiguracionTiempoRequest $request, int $id_servicio): JsonResponse
    {
        try {
            $dto = UpdateConfiguracionTiempoDTO::fromRequest($request);
            $this->configuracionTiempoService->updateTiempos($id_servicio, $dto);

            return response()->json([
                'status' => 200,
                'message' => 'Configuración actualizada correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al actualizar la configuración',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreConfiguracionTiempoRequest $request, int $id_servicio): JsonResponse
    {
        $dto = StoreConfiguracionTiempoDTO::fromRequest($request);
        $config = $this->configuracionTiempoService->storeTiempo($id_servicio, $dto);

        return response()->json([
            'status' => 200,
            'data' => new ConfiguracionTiempoResource($config)
        ], 200);
    }

    public function destroy(Request $request, int $id_servicio, string $tipo, int $numero_mensaje): JsonResponse
    {
        $deleted = $this->configuracionTiempoService->deleteTiempo($id_servicio, $tipo, $numero_mensaje);

        if ($deleted) {
            return response()->json(['status' => 200, 'message' => 'Eliminado'], 200);
        }

        return response()->json(['status' => 404, 'message' => 'No encontrado'], 404);
    }
}
