<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServicioService;
use App\DTOs\Servicio\ServicioFiltersDTO;
use App\DTOs\Servicio\CreateServicioDTO;
use App\DTOs\Servicio\UpdateServicioDTO;
use App\Http\Resources\ServicioResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiciosController extends Controller
{
    public function __construct(
        private ServicioService $servicioService
    ) {}

    public function get(Request $request)
    {
        try {
            $filters = ServicioFiltersDTO::fromRequest($request);
            $servicios = $this->servicioService->getServicios($filters);
            
            return ServicioResource::collection($servicios)->additional([
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $dto = CreateServicioDTO::fromRequest($request);
        $this->servicioService->createServicio($dto);

        return response()->json([
            'message' => 'Servicio creado exitosamente'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100',
                'descripcion' => 'required|string|max:200'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dto = UpdateServicioDTO::fromRequest($request);
            $servicio = $this->servicioService->updateServicio((int)$id, $dto);

            return response()->json([
                'status' => true,
                'message' => 'Servicio actualizado exitosamente',
                'data' => $servicio
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Servicio no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->servicioService->deleteServicio((int)$id);
            
            return response()->json([
                'message' => 'Servicio eliminado exitosamente'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Servicio no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
