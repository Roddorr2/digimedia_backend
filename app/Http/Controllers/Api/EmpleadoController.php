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
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Cloudinary\Cloudinary;
use App\Mail\CredencialesEmpleadoMail;
use Illuminate\Support\Facades\Auth;

class EmpleadoController extends Controller
{
    private function checkPermissionMiddleware(int $id)
    {
        $empleado = Empleado::where('id_empleado', $id)->first();

        if (!$empleado) {
            return response()->json([
                "status" => 404,
                "message" => "Empleado no encontrado"
            ], 404);
        }

        $currentEmployee = Empleado::where('id_empleado', Auth::user()->id)->first();
        $hasPermissonToModified = $empleado->canBeModifiedBy($currentEmployee);

        if (!$hasPermissonToModified) {
            Log::warning("Intento no autorizado de modificar empleado restringido", [
                'target_id' => $id,
                'target_email' => $empleado->email,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email
            ]);

            return response()->json([
                "status" => 403,
                "message" => "No tienes permiso para modificar este empleado"
            ], 403);
        }

        return null;
    }

    public function getById(GetEmpleadoByIdRequest $request, int $id)
    {
        $empleado = Empleado::with('rol')->where('id_empleado', $id)->first();

        if (!$empleado) {
            return response()->json(["status" => 404, "message" => "Empleado no encontrado"]);
        }

        // Obtener el usuario autenticado y verificar si es admin
        $currentUser = Auth::user();
        $currentEmpleado = Empleado::with('rol')->where('id_user', $currentUser->id)->first();
        $isAdmin = $currentEmpleado && strtolower($currentEmpleado->rol->nombre) === 'administrador';

        // Transformar DNI si el usuario NO es admin
        if (!$isAdmin && $empleado->dni) {
            $length = strlen($empleado->dni);
            if ($length > 4) {
                $empleado->dni = str_repeat('*', $length - 4) . substr($empleado->dni, -4);
            }
        }

        return response()->json([
            "status" => 200,
            "data" => $empleado
        ]);
    }

    public function getAllByPage(Request $request)
    {
        try {
            /**
             * Parámetros de la request
             * De no enviar tales parámetros en la petición, se establece valores por defecto
             */
            $search = $request->get('search', '');
            $rol = $request->get('rol', 'all');
            $pagination = $request->get('limit', 5);
            $sortBy = $request->get('sortBy', 'id_empleado');
            $sortOrder = $request->get('sortOrder', 'asc');

            // Obtener el empleado autenticado y verificar si es admin
            $currentUser = Auth::user();
            $currentEmpleado = Empleado::with('rol')->where('id_user', $currentUser->id)->first();
            $isAdmin = $currentEmpleado && strtolower($currentEmpleado->rol->nombre) === 'administrador';

            $data = Empleado::with('rol', 'subtipoAdmin');

            if(!empty($search) && trim($search) !== '')
            {
                $data->where(function($subQuery) use ($search)
                {
                   $subQuery->where('nombre', 'LIKE', '%' . $search . '%')
                            ->orWhere('apellido', 'LIKE', '%' . $search . '%')
                            ->orWhere('email', 'LIKE', '%' . $search . '%')
                            ->orWhere('dni', 'LIKE', '%' . $search . '%')
                            ->orWhere('telefono', 'LIKE', '%' . $search . '%'); 
                });
            }
            if($rol !== 'all' && !empty($rol))
            {
                $data->where('id_rol', (int)$rol);
            }
            $data->orderBy($sortBy, $sortOrder);

            $empleados = $data->paginate($pagination);
            $empleados->getCollection()->transform(function ($empleado) use ($isAdmin) {
                // Transformar DNI según el rol del usuario autenticado
                $dni = $empleado->dni;
                if (!$isAdmin && $dni) {
                    // Ocultar DNI parcialmente: mostrar solo los últimos 4 dígitos
                    $length = strlen($dni);
                    if ($length > 4) {
                        $dni = str_repeat('*', $length - 4) . substr($dni, -4);
                    }
                }

                return [
                    'id_empleado' => $empleado->id_empleado,
                    'nombre' => $empleado->nombre,
                    'apellido' => $empleado->apellido,
                    'email' => $empleado->email,
                    'dni' => $dni,
                    'telefono' => $empleado->telefono,
                    'rol' => $empleado->rol->nombre,
                    'id_rol' => $empleado->rol->id_rol,
                    'subtipo_admin' =>$empleado->subtipoAdmin
                ];
            });

            return response()->json([
                "status" => 200,
                'data' => $empleados->items(),
                'total' => $empleados->total(),
                'page' => $empleados->currentPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(StoreEmpleadoRequest $request)
    {
        DB::beginTransaction();
        try {

            $password = $this->createPassword($request->dni, $request->nombre, $request->apellido);

            $user = User::create([
                'name' => $request->nombre . ' ' . $request->apellido,
                'email' => $request->email,
                'password' => Hash::make($password),
            ]);

            $empleado = Empleado::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'dni' => $request->dni,
                'telefono' => $request->telefono,
                'id_user' => $user->id,
                'id_rol' => $request->id_rol,
            ]);

            DB::commit();

            Mail::to($user->email)->send(new CredencialesEmpleadoMail($user, $password));

            return response()->json([
                "status" => 200,
                "message" => "Empleado creado correctamente",
                "user" => $user,
                "empleado" => $empleado,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => 500,
                "message" => "Error al crear empleado",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    private function createPassword(string $dni, string $nombre, string $apellidos)
    {

        $apellidoIniciales = strtoupper(substr($nombre, 0, 2));
        $nombreIniciales = strtolower(substr($apellidos, 0, 2));
        $dniParte = substr($dni, -3);

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);

        $password = "{$apellidoIniciales}{$dniParte}";

        for ($i = 0; $i < 5; $i++) {
            $password .= $characters[rand(0, $charactersLength - 1)];
        }

        $password .= $nombreIniciales;

        return $password;
    }

    public function update(UpdateEmpleadoRequest $request, int $id)
    {
        $permissionCheck = $this->checkPermissionMiddleware($id);
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $empleado = Empleado::where('id_empleado', $id)->first();

        if (!$empleado) {
            return response()->json([
                "status" => 404,
                "message" => "Empleado no encontrado"
            ]);
        }

        $user = User::find($empleado->id_user);

        if ($user) {
            if ($request->has('email') && $request->email != $empleado->email) {
                $user->email = $request->email;
            }

            if (
                ($request->has('nombre') && $request->nombre != $empleado->nombre) ||
                ($request->has('apellido') && $request->apellido != $empleado->apellido)
            ) {
                $nombre   = $request->has('nombre') ? $request->nombre : $empleado->nombre;
                $apellido = $request->has('apellido') ? $request->apellido : $empleado->apellido;
                $user->name = $nombre . ' ' . $apellido;
            }

            $user->save();
        }

        $empleado->update($request->all());
        if($request->id_rol != 1)
        {
            $empleado->id_subtipo_admin = null;
            $empleado->save();
        }
        return response()->json([
            "status"  => 200,
            "message" => "Empleado actualizado correctamente",
            "data"    => $empleado
        ]);
    }


    public function generateUploadSignature(GenerateEmpleadoUploadSignatureRequest $request, int $id)
    {
        $authUser     = Auth::user();
        $authEmpleado = $authUser->empleado;

        if (!$authEmpleado) {
            return response()->json(['status' => 403, 'message' => 'No se encontró el empleado asociado'], 403);
        }

        $targetEmpleado = Empleado::where('id_empleado', $id)->first();
        if (!$targetEmpleado) {
            return response()->json(['status' => 404, 'message' => 'Empleado no encontrado'], 404);
        }

        if (!$targetEmpleado->canBeModifiedBy($authEmpleado)) {
            Log::warning('Intento no autorizado de generar firma de subida de imagen', [
                'target_id' => $id,
                'user_id'   => $authUser->id,
            ]);
            return response()->json(['status' => 403, 'message' => 'No tienes permiso para subir imágenes en este perfil'], 403);
        }

        $paramsToSign = $request->all();

        // El backend impone los parámetros críticos; el frontend no puede redefinirlos
        $paramsToSign['folder']    = "empleados/perfiles/{$id}";
        $paramsToSign['public_id'] = 'profile';
        $paramsToSign['overwrite'] = 'true';

        // Parámetros excluidos del cálculo de firma según la documentación de Cloudinary
        $excludedKeys   = ['file', 'cloud_name', 'resource_type', 'api_key'];
        $filteredParams = array_filter($paramsToSign, fn($key) => !in_array($key, $excludedKeys), ARRAY_FILTER_USE_KEY);

        // Normalizar booleanos PHP a cadenas para que coincidan con el valor que el widget envía a Cloudinary
        foreach ($filteredParams as $key => $value) {
            if (is_bool($value)) {
                $filteredParams[$key] = $value ? 'true' : 'false';
            }
        }

        ksort($filteredParams);

        $signatureString = implode('&', array_map(
            fn($k, $v) => "{$k}={$v}",
            array_keys($filteredParams),
            $filteredParams
        ));
        $signatureString .= env('CLOUDINARY_SECRET');

        return response()->json([
            'signature' => hash('sha256', $signatureString),
        ]);
    }

    public function updateProfileImage(UpdateEmpleadoProfileImageRequest $request, int $id)
    {
        $authUser     = Auth::user();
        $authEmpleado = $authUser->empleado;

        if (!$authEmpleado) {
            return response()->json(['status' => 403, 'message' => 'No se encontró el empleado asociado'], 403);
        }

        $empleado = Empleado::where('id_empleado', $id)->first();
        if (!$empleado) {
            return response()->json(["status" => 404, "message" => "Empleado no encontrado"], 404);
        }

        if (!$empleado->canBeModifiedBy($authEmpleado)) {
            Log::warning('Intento no autorizado de actualizar imagen de perfil', [
                'target_id' => $id,
                'user_id'   => $authUser->id,
            ]);
            return response()->json(['status' => 403, 'message' => 'No tienes permiso para modificar este perfil'], 403);
        }

        // Validar que el public_id pertenezca exactamente a la carpeta del empleado
        $expectedPublicId = "empleados/perfiles/{$id}/profile";
        if ($request->public_id !== $expectedPublicId) {
            Log::warning('public_id no coincide con la carpeta esperada del empleado', [
                'expected' => $expectedPublicId,
                'received' => $request->public_id,
                'user_id'  => $authUser->id,
            ]);
            return response()->json(['status' => 403, 'message' => 'Imagen no autorizada para este perfil'], 403);
        }

        try {
            DB::beginTransaction();

            $empleado->imagen_perfil     = $request->public_id;
            $empleado->imagen_perfil_url = $request->secure_url;
            $empleado->save();

            DB::commit();

            return response()->json([
                "status"  => 200,
                "message" => "Imagen actualizada correctamente",
                "data"    => [
                    'public_id' => $empleado->imagen_perfil,
                    'url'       => $empleado->imagen_perfil_url,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error actualizando imagen: " . $e->getMessage());
            return response()->json([
                "status"  => 500,
                "message" => "Error al actualizar la imagen",
                "error"   => env('APP_DEBUG') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }


    public function updatePass(UpdateEmpleadoPasswordRequest $request, int $id)
    {
        $permissionCheck = $this->checkPermissionMiddleware($id);
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $empleado = Empleado::where('id_empleado', $id)->first();

        if (!$empleado) {
            return response()->json(["status" => 404, "message" => "Empleado no encontrado"]);
        }

        $userId = $empleado->id_user;

        return $this->updatePass1($request, $userId);
    }

    private function updatePass1(UpdateEmpleadoPasswordRequest $request, int $id)
    {
        $response = User::where(["id" => intval($id)])->update(["password" => Hash::make($request->password)]);

        if ($response) {
            return response()->json(["status" => 200, "message" => "Registro actualizado correctamente"]);
        }
    }

    public function verifyPassword(VerifyEmpleadoPasswordRequest $request)
    {
        try {
            $empleado = Empleado::with('user')->findOrFail($request->id_empleado);

            if (!$empleado->user) {
                return response()->json([
                    'valid' => false,
                    'message' => 'No se encontró el usuario asociado al empleado'
                ], 404);
            }

            if (!Hash::check($request->currentPassword, $empleado->user->password)) {
                return response()->json([
                    'valid' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ], 400);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Contraseña verificada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(DeleteEmpleadoRequest $request, int $id)
    {
        $permissionCheck = $this->checkPermissionMiddleware($id);
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $empleado = Empleado::where('id_empleado', $id)->first();

        if (!$empleado) {
            return response()->json([
                "status" => 404,
                "message" => "Empleado no encontrado"
            ], 404);
        }


        $user = User::find($empleado->id_user);
        if ($user) {
            Log::info("Eliminando usuario vinculado con ID: " . $user->id);
            $user->delete();
        }

        Log::info("Eliminando empleado con ID: $id");
        $empleado->delete();

        Log::info("Empleado eliminado correctamente");
        return response()->json([
            "status" => 200,
            "message" => "Empleado eliminado correctamente"
        ], 200);
    }

    public function deleteProfileImage(int $id)
    {
        $authUser     = Auth::user();
        $authEmpleado = $authUser->empleado;

        if (!$authEmpleado) {
            return response()->json(['status' => 403, 'message' => 'No se encontró el empleado asociado'], 403);
        }

        $empleado = Empleado::where('id_empleado', $id)->first();
        if (!$empleado) {
            return response()->json(['status' => 404, 'message' => 'Empleado no encontrado'], 404);
        }

        if (!$empleado->canBeModifiedBy($authEmpleado)) {
            Log::warning('Intento no autorizado de eliminar imagen de perfil', [
                'target_id' => $id,
                'user_id'   => $authUser->id,
            ]);
            return response()->json(['status' => 403, 'message' => 'No tienes permiso para modificar este perfil'], 403);
        }

        try {
            if ($empleado->imagen_perfil) {
                Log::info('Eliminando imagen de perfil:', ['public_id' => $empleado->imagen_perfil]);
                try {
                    $cloudinary = new Cloudinary();
                    $result = $cloudinary->uploadApi()->destroy($empleado->imagen_perfil);
                    Log::info('Resultado de eliminación en Cloudinary:', ['result' => $result]);
                } catch (\Exception $e) {
                    Log::warning("Error al eliminar imagen en Cloudinary: " . $e->getMessage());
                }

                $empleado->imagen_perfil     = null;
                $empleado->imagen_perfil_url = null;
                $empleado->save();
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Imagen eliminada correctamente',
            ]);
        } catch (\Exception $e) {
            Log::error("Error eliminando imagen de perfil: " . $e->getMessage());
            return response()->json([
                'status'  => 500,
                'message' => 'Error al eliminar la imagen',
                'error'   => env('APP_DEBUG') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }
}
