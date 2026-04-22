<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subservicio;
use Illuminate\Http\Request;

class SubservicioController extends Controller
{
    /**
     * Listar todos los subservicios con su servicio anidado
     * Usado por el dashboard para poblar selectores de servicio/subservicio
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $subservicios = Subservicio::with('servicio')
                ->orderBy('id_servicio')
                ->orderBy('id_subservicio')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $subservicios
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
    public function byServicio($id_servicio)
    {
        try {
            $subservicios = Subservicio::with('servicio')
                ->byServicio($id_servicio)
                ->orderBy('id_subservicio')
                ->get();

            if ($subservicios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "No se encontraron subservicios para el servicio {$id_servicio}"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => $subservicios
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
