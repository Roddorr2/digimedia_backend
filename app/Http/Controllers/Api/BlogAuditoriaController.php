<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogAuditoriaResource;
use App\Services\BlogAuditoriaService;
use App\DTOs\BlogAuditoria\FiltrosAuditoriaDTO;
use Illuminate\Http\Request;

class BlogAuditoriaController extends Controller
{
    public function __construct(
        private BlogAuditoriaService $auditoriaService
    ) {}

    public function show(Request $request)
    {
        try {
            $filtros = FiltrosAuditoriaDTO::fromRequest($request);
            $resultado = $this->auditoriaService->obtenerAuditorias($filtros);

            if (!$resultado['has_results']) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No se encontraron registros de auditoría'
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => BlogAuditoriaResource::collection($resultado['auditorias']),
                'meta' => [
                    'current_page' => $resultado['auditorias']->currentPage(),
                    'per_page' => $resultado['auditorias']->perPage(),
                    'total' => $resultado['auditorias']->total(),
                    'last_page' => $resultado['auditorias']->lastPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}