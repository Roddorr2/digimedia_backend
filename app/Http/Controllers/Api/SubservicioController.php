<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubservicioService;
use App\Http\Resources\SubservicioResource;
use Illuminate\Http\JsonResponse;

class SubservicioController extends Controller
{
    public function __construct(
        private SubservicioService $subservicioService
    ) {}

    /**
     * Listar todos los subservicios con su servicio anidado
     * Usado por el dashboard para poblar selectores de servicio/subservicio
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $subservicios = $this->subservicioService->getSubservicios();

            return response()->json([
                'success' => true,
                'data'    => SubservicioResource::collection($subservicios)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener subservicios',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar subservicios de un servicio específico
     * Usado para cascada: al seleccionar un servicio, cargar sus subservicios
     *
     * @param int $id_servicio
     * @return \Illuminate\Http\JsonResponse
     */
    public function byServicio($id_servicio): JsonResponse
    {
        try {
            $subservicios = $this->subservicioService->getSubserviciosByServicio((int)$id_servicio);

            if ($subservicios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "No se encontraron subservicios para el servicio {$id_servicio}"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => SubservicioResource::collection($subservicios)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener subservicios',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
