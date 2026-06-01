<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Empleado\DeleteEmpleadoRequest;
use App\Http\Requests\Empleado\GenerateEmpleadoUploadSignatureRequest;
use App\Http\Requests\Empleado\GetEmpleadoByIdRequest;
use App\Http\Requests\Empleado\StoreEmpleadoRequest;
use App\Http\Requests\Empleado\UpdateEmpleadoPasswordRequest;
use App\Http\Requests\Empleado\UpdateEmpleadoProfileImageRequest;
use App\Http\Requests\Empleado\UpdateEmpleadoRequest;
use App\Http\Requests\Empleado\VerifyEmpleadoPasswordRequest;
use App\Services\EmpleadoService;
use App\DTOs\Empleado\CreateEmpleadoDTO;
use App\DTOs\Empleado\UpdateEmpleadoDTO;
use App\DTOs\Empleado\VerifyEmpleadoPasswordDTO;
use App\DTOs\Empleado\UpdateEmpleadoPasswordDTO;
use App\DTOs\Empleado\UpdateProfileImageDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class EmpleadoController extends Controller
{
    public function __construct(
        private EmpleadoService $empleadoService
    ) {}

    public function getById(GetEmpleadoByIdRequest $request, $id): JsonResponse
    {
        try {
            $empleado = $this->empleadoService->getEmpleadoById((int)$id, Auth::user());

            return response()->json([
                "status" => 200,
                "data" => $empleado
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(["status" => 404, "message" => "Empleado no encontrado"]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al obtener empleado",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function getAllByPage(Request $request): JsonResponse
    {
        try {
            $params = [
                'search' => $request->get('search', ''),
                'rol' => $request->get('rol', 'all'),
                'limit' => $request->get('limit', 5),
                'sortBy' => $request->get('sortBy', 'id_empleado'),
                'sortOrder' => $request->get('sortOrder', 'asc'),
            ];

            $result = $this->empleadoService->getEmpleadosPaginated($params, Auth::user());

            return response()->json([
                "status" => 200,
                'data' => $result['data'],
                'total' => $result['total'],
                'page' => $result['page']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(StoreEmpleadoRequest $request): JsonResponse
    {
        try {
            $dto = CreateEmpleadoDTO::fromRequest($request);
            $result = $this->empleadoService->createEmpleado($dto);

            return response()->json([
                "status" => 200,
                "message" => "Empleado creado correctamente",
                "user" => $result['user'],
                "empleado" => $result['empleado'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al crear empleado",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateEmpleadoRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdateEmpleadoDTO::fromRequest($request);
            $empleado = $this->empleadoService->updateEmpleado((int)$id, $dto, Auth::user());

            return response()->json([
                "status"  => 200,
                "message" => "Empleado actualizado correctamente",
                "data"    => $empleado
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => "Empleado no encontrado"
            ], 404);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $statusCode = in_array($code, [400, 403, 422]) ? $code : 500;
            return response()->json([
                "status" => $statusCode,
                "message" => $e->getMessage()
            ], $statusCode);
        }
    }

    public function generateUploadSignature(GenerateEmpleadoUploadSignatureRequest $request, $id): JsonResponse
    {
        try {
            $signature = $this->empleadoService->generateUploadSignature((int)$id, $request->all(), Auth::user());

            return response()->json([
                'signature' => $signature,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Empleado no encontrado'], 404);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $statusCode = in_array($code, [403, 422]) ? $code : 500;
            return response()->json(['status' => $statusCode, 'message' => $e->getMessage()], $statusCode);
        }
    }

    public function updateProfileImage(UpdateEmpleadoProfileImageRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdateProfileImageDTO::fromRequest($request);
            $result = $this->empleadoService->updateProfileImage((int)$id, $dto, Auth::user());

            return response()->json([
                "status"  => 200,
                "message" => "Imagen actualizada correctamente",
                "data"    => [
                    'public_id' => $result['public_id'],
                    'url'       => $result['url'],
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(["status" => 404, "message" => "Empleado no encontrado"], 404);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $statusCode = in_array($code, [403, 422]) ? $code : 500;
            return response()->json([
                "status"  => $statusCode,
                "message" => $e->getMessage(),
                "error"   => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], $statusCode);
        }
    }

    public function updatePass(UpdateEmpleadoPasswordRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdateEmpleadoPasswordDTO::fromRequest($request);
            $this->empleadoService->updatePassword((int)$id, $dto);

            return response()->json(["status" => 200, "message" => "Registro actualizado correctamente"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(["status" => 404, "message" => "Empleado no encontrado"], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al actualizar contraseña",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function verifyPassword(VerifyEmpleadoPasswordRequest $request): JsonResponse
    {
        try {
            $dto = VerifyEmpleadoPasswordDTO::fromRequest($request);
            $result = $this->empleadoService->verifyPassword($dto);

            if (!$result['valid']) {
                return response()->json([
                    'valid' => false,
                    'message' => $result['message']
                ], $result['status']);
            }

            return response()->json([
                'valid' => true,
                'message' => $result['message']
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(DeleteEmpleadoRequest $request, $id): JsonResponse
    {
        try {
            $this->empleadoService->deleteEmpleado((int)$id, Auth::user());

            return response()->json([
                "status" => 200,
                "message" => "Empleado eliminado correctamente"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => "Empleado no encontrado"
            ], 404);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $statusCode = in_array($code, [403, 422]) ? $code : 500;
            return response()->json([
                "status" => $statusCode,
                "message" => $e->getMessage()
            ], $statusCode);
        }
    }

    public function deleteProfileImage($id): JsonResponse
    {
        try {
            $this->empleadoService->deleteProfileImage((int)$id, Auth::user());

            return response()->json([
                'status'  => 200,
                'message' => 'Imagen eliminada correctamente',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Empleado no encontrado'], 404);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $statusCode = in_array($code, [403, 422]) ? $code : 500;
            return response()->json([
                'status'  => $statusCode,
                'message' => $e->getMessage(),
                'error'   => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], $statusCode);
        }
    }
}
