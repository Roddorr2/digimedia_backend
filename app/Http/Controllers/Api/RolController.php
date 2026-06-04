<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rol\StoreRolRequest;
use App\Http\Requests\Rol\UpdateRolRequest;
use App\Http\Requests\Rol\SyncPermisosRequest;
use App\Http\Resources\RolResource;
use App\Http\Resources\PermisoResource;
use App\Services\RolService;
use App\DTOs\Rol\StoreRolDTO;
use App\DTOs\Rol\UpdateRolDTO;
use App\DTOs\Rol\SyncPermisosDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RolController extends Controller
{
    public function __construct(
        private RolService $rolService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $roles = $this->rolService->getRoles();
            return response()->json([
                'status' => 200,
                'data' => RolResource::collection($roles),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al obtener roles',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRolRequest $request
     * @return JsonResponse
     */
    public function store(StoreRolRequest $request): JsonResponse
    {
        try {
            $dto = StoreRolDTO::fromRequest($request);
            $rol = $this->rolService->createRol($dto);

            return response()->json([
                'status' => 201,
                'message' => 'Rol creado correctamente',
                'data' => new RolResource($rol),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al crear rol',
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
            $rol = $this->rolService->getRolById((int)$id, ['permisos']);
            return response()->json([
                'status' => 200,
                'data' => new RolResource($rol),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'error' => 'Rol no encontrado',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRolRequest $request
     * @param mixed $id
     * @return JsonResponse
     */
    public function update(UpdateRolRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdateRolDTO::fromRequest($request);
            $rol = $this->rolService->updateRol((int)$id, $dto);

            return response()->json([
                'status' => 200,
                'message' => 'Rol actualizado correctamente',
                'data' => new RolResource($rol),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'error' => 'Rol no encontrado',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al actualizar rol',
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
            $this->rolService->deleteRol((int)$id);

            return response()->json([
                'status' => 200,
                'message' => 'Rol eliminado correctamente',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'error' => 'Rol no encontrado',
            ], 404);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => 400,
                'error' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al eliminar rol',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display permissions of the specified role.
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function getPermisos($id): JsonResponse
    {
        try {
            $permisos = $this->rolService->getPermisosDeRol((int)$id);
            
            return response()->json([
                'status' => 200,
                'data' => PermisoResource::collection($permisos),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'error' => 'Rol no encontrado',
            ], 404);
        }
    }

    /**
     * Synchronize permissions of the specified role.
     *
     * @param SyncPermisosRequest $request
     * @param mixed $id
     * @return JsonResponse
     */
    public function syncPermisos(SyncPermisosRequest $request, $id): JsonResponse
    {
        try {
            $dto = SyncPermisosDTO::fromRequest($request);
            $permisos = $this->rolService->syncPermisosDeRol((int)$id, $dto);

            return response()->json([
                'status' => 200,
                'message' => 'Permisos actualizados correctamente',
                'data' => PermisoResource::collection($permisos),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'error' => 'Rol no encontrado',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Error al actualizar permisos',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
