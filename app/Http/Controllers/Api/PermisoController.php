<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permiso\StorePermisoRequest;
use App\Http\Requests\Permiso\UpdatePermisoRequest;
use App\Http\Resources\PermisoResource;
use App\Services\PermisoService;
use App\DTOs\Permiso\StorePermisoDTO;
use App\DTOs\Permiso\UpdatePermisoDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermisoController extends Controller
{
    public function __construct(
        private PermisoService $permisoService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $permisos = $this->permisoService->getPermisos();
            return response()->json([
                'status' => 200,
                'data' => PermisoResource::collection($permisos),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al obtener permisos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePermisoRequest $request
     * @return JsonResponse
     */
    public function store(StorePermisoRequest $request): JsonResponse
    {
        try {
            $dto = StorePermisoDTO::fromRequest($request);
            $permiso = $this->permisoService->createPermiso($dto);

            return response()->json([
                'status' => 201,
                'message' => 'Permiso creado correctamente',
                'data' => new PermisoResource($permiso),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al crear permiso',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $permiso = $this->permisoService->getPermisoById((int)$id);
            return response()->json([
                'status' => 200,
                'data' => new PermisoResource($permiso),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'error' => 'Permiso no encontrado',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePermisoRequest $request
     * @param mixed $id
     * @return JsonResponse
     */
    public function update(UpdatePermisoRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdatePermisoDTO::fromRequest($request);
            $permiso = $this->permisoService->updatePermiso((int)$id, $dto);

            return response()->json([
                'status' => 200,
                'message' => 'Permiso actualizado correctamente',
                'data' => new PermisoResource($permiso),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'error' => 'Permiso no encontrado',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al actualizar permiso',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->permisoService->deletePermiso((int)$id);

            return response()->json([
                'status' => 200,
                'message' => 'Permiso eliminado correctamente',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'error' => 'Permiso no encontrado',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al eliminar permiso',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
