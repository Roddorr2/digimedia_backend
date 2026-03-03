<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Cloudinary\Cloudinary;
use App\Mail\CredencialesEmpleadoMail;
use Illuminate\Support\Facades\Auth;

class EmpleadoController extends Controller
{
    private function checkPermissionMiddleware($id)
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

    public function getById($id)
    {
        $validate = Validator::make(["id" => $id], [
            "id" => "required|numeric",
        ]);

        if ($validate->fails()) {
            return response()->json(["status" => 422, "message" => "Error de validación", "Errors" => $validate->errors()]);
        }

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

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:empleados|unique:users',
            'dni' => 'required|string|max:20|unique:empleados',
            'telefono' => 'nullable|string|max:20',
            'id_rol' => 'required|exists:roles,id_rol',
        ],  [
            'email.unique' => 'El correo ya esta en uso.',
            'dni.unique' => 'El DNI ya está registrado.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

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

    public function update(Request $request, $id)
    {
        $validate = Validator::make(["id" => $id], [
            "id" => "required|numeric",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "status" => 422,
                "message" => "Error de validación",
                "Errors" => $validate->errors()
            ]);
        }

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

        if((int)auth() -> id() !== (int)$empleado ->id_user){
            return response()->json([
                "message" => "No es posible editar perfiles que no sean tuyos."
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre'    => 'sometimes|string|max:255',
            'apellido'  => 'sometimes|string|max:255',
            'email'     => 'sometimes|string|email|max:255|unique:empleados,email,' . $id . ',id_empleado|unique:users,email,' . $empleado->id_user,
            'dni'       => 'sometimes|string|max:20|unique:empleados,dni,' . $id . ',id_empleado',
            'telefono'  => 'nullable|string|max:20',
            'id_rol'    => 'sometimes|exists:roles,id_rol',
        ], [
            'nombre.string' => 'Debes ingresar un nombre',
            'apellido.string' => 'Debes ingresar un apellido',
            'email.string' => 'Debes ingresar un email',
            'dni.string' => 'Debes ingresar un DNI',
            'dni.unique' => 'Este número de DNI ya ha sido registrado.',
            'email.unique' => 'Este correo ya está en uso.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
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


    public function updateProfileImage(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'public_id' => 'required|string',
            'secure_url' => 'required|url'
        ]);

        if ($validate->fails()) {
            return response()->json([
                "status" => 422,
                "message" => "Error de validación",
                "errors" => $validate->errors()
            ], 422);
        }

        try {
            $empleado = Empleado::where('id_empleado', $id)->first();
            if (!$empleado) {
                return response()->json([
                    "status" => 404,
                    "message" => "Empleado no encontrado"
                ], 404);
            }

            DB::beginTransaction();

            if ($empleado->imagen_perfil) {
                try {

                    $cloudinary = new Cloudinary();

                    $result = $cloudinary->uploadApi()->destroy($empleado->imagen_perfil);
                } catch (\Exception $e) {
                    Log::warning("Error al eliminar imagen anterior, continuando con actualización: " . $e->getMessage());
                }
            }

            $empleado->imagen_perfil_url = null;
            $empleado->imagen_perfil = $request->public_id;
            $empleado->imagen_perfil_url = $request->secure_url;
            $empleado->save();

            DB::commit();

            return response()->json([
                "status" => 200,
                "message" => "Imagen actualizada correctamente",
                "data" => [
                    'public_id' => $empleado->imagen_perfil,
                    'url' => $empleado->imagen_perfil_url,
                    'version' => time()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error actualizando imagen: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                "status" => 500,
                "message" => "Error al actualizar la imagen",
                "error" => env('APP_DEBUG') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }


    public function updatePass(Request $request, $id)
    {
        $validate = Validator::make(["id" => $id], [
            "id" => "required|numeric",
        ]);

        if ($validate->fails()) {
            return response()->json(["status" => 422, "message" => "Error de validación", "Errors" => $validate->errors()]);
        }

        $empleado = Empleado::where('id_empleado', $id)->first();

        if (!$empleado) {
            return response()->json(["status" => 404, "message" => "Empleado no encontrado"]);
        }

        $userId = $empleado->id_user;

        return $this->updatePass1($request, $userId);
    }

    private function updatePass1(Request $request, $id)
    {
        $validate = Validator::make(["id" => $request->id], [
            "id" => "required|numeric",
        ]);

        if ($validate->fails()) {
            return response()->json(["status" => 422, "message" => "Error de validación", "Errors" => $validate->errors()]);
        }

        $validate = Validator::make($request->all(), [
            "password" => "required|string|min:4",
        ]);

        if ($validate->fails()) {
            return response()->json(["status" => 422, "message" => "Error de validación", "Errors" => $validate->errors(), "data" => $request->all()]);
        }

        $response = User::where(["id" => intval($id)])->update(["password" => Hash::make($request->password)]);

        if ($response) {
            return response()->json(["status" => 200, "message" => "Registro actualizado correctamente"]);
        }
    }

    public function verifyPassword(Request $request)
    {
        try {
            $request->validate([
                'currentPassword' => 'required',
                'id_empleado' => 'required|exists:empleados,id_empleado'
            ]);

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

    public function delete(Request $request, $id)
    {
        $validate = Validator::make(["id" => $id], [
            "id" => "required|numeric",
        ]);

        if ($validate->fails()) {
            Log::error("Validación fallida: ", $validate->errors()->toArray());
            return response()->json([
                "status" => 422,
                "message" => "Error de validación",
                "errors" => $validate->errors()
            ], 422);
        }

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

    public function deleteProfileImage($id)
    {
        try {
            $empleado = Empleado::where('id_empleado', $id)->first();

            if (!$empleado) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Empleado no encontrado'
                ], 404);
            }

            if ($empleado->imagen_perfil) {
                Log::info('Intentando eliminar imagen de perfil:', ['public_id' => $empleado->imagen_perfil]);

                try {
                    $cloudinary = new Cloudinary();

                    $result = $cloudinary->uploadApi()->destroy($empleado->imagen_perfil);
                    Log::info('Resultado de eliminación:', ['result' => $result]);
                } catch (\Exception $e) {
                    Log::warning("Error al eliminar imagen de Cloudinary: " . $e->getMessage());
                    // Continuamos con la actualización en la base de datos
                }

                $empleado->imagen_perfil = null;
                $empleado->imagen_perfil_url = null;
                $empleado->save();
            }

            return response()->json([
                'status' => 200,
                'message' => 'Imagen eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error("Error eliminando imagen de perfil: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error al eliminar la imagen',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }
}
