# Fixes Rápidos - Vulnerabilidades de Seguridad

## TABLA DE CONTENIDOS
1. [Resources (Para datos sensibles)](#1-crear-resources)
2. [Models - Configuración segura](#2-configurar-models)
3. [Controllers - Autorización](#3-validar-autorizacion)
4. [Validaciones mejoradas](#4-validaciones-mejoradas)

---

## 1. CREAR RESOURCES

### UserResource.php
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // ✅ NO INCLUIR: password, remember_token, created_at, updated_at
        ];
    }
}
```

### EmpleadoResource.php (Mejorado)
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpleadoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = auth()?->user()?->empleado?->rol->nombre === 'administrador';
        
        $data = [
            'id_empleado' => $this->id_empleado,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'imagen_perfil_url' => $this->imagen_perfil_url,
            'rol' => $this->when($this->rol, $this->rol->nombre),
        ];
        
        // ✅ Solo admin ve DNI completo
        if ($isAdmin) {
            $data['dni'] = $this->dni;
        } else {
            // Ocultar DNI parcialmente para no-admin
            $dni = $this->dni;
            if ($dni && strlen($dni) > 4) {
                $data['dni'] = str_repeat('*', strlen($dni) - 4) . substr($dni, -4);
            }
        }
        
        return $data;
        
        // ✅ NO EXPONER:
        // 'id_user' (relación interna)
        // 'id_subtipo_admin' (estructura interna)
        // 'created_at', 'updated_at' (timing info)
    }
}
```

### ContactanosResource.php
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactanosResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_contactanos' => $this->id_contactanos,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'numero' => $this->numero,
            'mensaje' => $this->mensaje,
            'estado' => (bool) $this->estado,
            // ✅ NO: timestamps
        ];
    }
}
```

### CardResource.php
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_card' => $this->id_card,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'public_image' => $this->public_image,
            'url_image' => $this->url_image,
            'estado_publicacion' => (bool) $this->estado_publicacion,
            'blog' => new BlogResource($this->whenLoaded('blog')),
            // ✅ NO: id_empleado, id_blog (internos)
        ];
    }
}
```

---

## 2. CONFIGURAR MODELS

### Empleado.php (Mejorado)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cloudinary\Cloudinary;
use App\Services\CacheService;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'dni',
        'telefono',
        'imagen_perfil',
        'imagen_perfil_url',
        'id_user',
        'id_rol',
        'id_subtipo_admin'
    ];

    // ✅ AGREGAR ESTA SECCIÓN
    protected $hidden = [
        'id_user',              // ❌ No exponer FK a users
        'id_subtipo_admin',     // ❌ Estructura interna
        'created_at',           // ❌ Timing info
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ... resto del código
}
```

### Card.php (Mejorado)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;
    
    protected $table = 'cards';
    protected $primaryKey = 'id_card';
    public $timestamps = true;  // ✅ Permitir timestamps

    protected $fillable = [
        'titulo',
        'descripcion',
        'public_image',
        'url_image',
        'id_plantilla',
        'id_blog',
        'id_empleado',
        'estado_publicacion'
    ];

    // ✅ AGREGAR
    protected $hidden = [
        'id_empleado',      // ❌ No exponer empleado
        'created_at',       // ❌ Timing
        'updated_at',       // ❌ Timing
    ];

    protected $casts = [
        'estado_publicacion' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ... resto del código
}
```

---

## 3. VALIDAR AUTORIZACIÓN

### AuthController.php (Mejorado - Login)
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\EmpleadoResource;
use App\Models\User;
use App\Models\Empleado;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'email' => 'required|string|email|max:255|unique:empleados|unique:users',
            'dni' => 'required|string|regex:/^\d{8}$/|unique:empleados',  // ✅ Formato DNI
            'telefono' => 'nullable|string|regex:/^9\d{8}$/',
            'id_rol' => 'required|exists:roles,id_rol',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->nombre . ' ' . $request->apellido,
                'email' => $request->email,
                'password' => Hash::make('1234'),
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

            $rol = Rol::find($request->id_rol);
            $abilities = [$rol->nombre];

            $token = $user->createToken('auth_token', $abilities)->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario registrado exitosamente',
                'user' => new UserResource($user),        // ✅ USO RESOURCE
                'empleado' => new EmpleadoResource($empleado), // ✅ USO RESOURCE
                'rol' => $rol->nombre,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            
            // ✅ NO EXPONER ERROR
            Log::error('Registration error', [
                'email' => $request->email,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al registrar usuario'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|min:4',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Esta cuenta no está registrada en Digimedia.'
                ], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'El email o la contraseña son incorrectos.'
                ], 401);
            }

            $empleado = $user->empleado;
            if (!$empleado || !$empleado->rol) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'El usuario no tiene un rol asignado'
                ], 403);
            }

            $empleado->load(['rol', 'subtipoAdmin']);

            $rol = $empleado->rol;
            $abilities = [$rol->nombre];
            $permisos = $rol->permisos()->pluck('nombre')->toArray();

            $token = $user->createToken('auth_token', $abilities)->plainTextToken;

            // ✅ Unset relación para evitar serialización accidental
            $user->unsetRelation('empleado');

            return response()->json([
                'status'   => 'success',
                'user'     => new UserResource($user),           // ✅ USO RESOURCE
                'empleado' => new EmpleadoResource($empleado),   // ✅ USO RESOURCE
                'rol'      => $rol->nombre,
                'permisos' => $permisos,
                'token'    => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Login error', ['email' => $request->email ?? 'unknown']);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error en el servidor'
                // ✅ NO: 'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### EmpleadoController.php (Mejorado - getById)
```php
<?php

class EmpleadoController extends Controller
{
    public function getById($id)
    {
        $validate = Validator::make(["id" => $id], [
            "id" => "required|numeric|min:1",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "status" => 422,
                "message" => "Error de validación",
                "errors" => $validate->errors()
            ], 422);
        }

        $empleado = Empleado::with('rol')->where('id_empleado', $id)->first();

        if (!$empleado) {
            return response()->json([
                "status" => 404,
                "message" => "Empleado no encontrado"
            ], 404);
        }

        // ✅ VALIDAR AUTORIZACIÓN (IDOR CHECK)
        $currentUser = Auth::user();
        $currentEmpleado = Empleado::where('id_user', $currentUser->id)->first();
        
        if (!$currentEmpleado) {
            return response()->json([
                "status" => 403,
                "message" => "No autorizado"
            ], 403);
        }

        $isAdmin = strtolower($currentEmpleado->rol->nombre) === 'administrador';

        // ✅ IDOR CHECK: Solo admin o el propio empleado puede ver sus datos
        if (!$isAdmin && $currentEmpleado->id_empleado !== (int)$id) {
            Log::warning('Unauthorized employee access attempt', [
                'requester_id' => $currentEmpleado->id_empleado,
                'target_id' => $id,
            ]);

            return response()->json([
                "status" => 403,
                "message" => "No tienes permiso para acceder a este empleado"
            ], 403);
        }

        // ✅ DEVOLVER CON RESOURCE
        return response()->json([
            "status" => 200,
            "data" => new EmpleadoResource($empleado)
        ]);
    }
}
```

### ContactanosController.php (Mejorado)
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactanosResource;
use App\Models\Contactanos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ContactanosController extends Controller
{
    public function get(Request $request)
    {
        // ✅ USAR RESOURCE
        $contactos = Contactanos::paginate(4);
        return response()->json(ContactanosResource::collection($contactos), 200);
    }

    public function getById($id)
    {
        $contacto = Contactanos::find($id);

        if (!$contacto) {
            return response()->json(['error' => 'Contacto no encontrado'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new ContactanosResource($contacto)  // ✅ USAR RESOURCE
        ], 200);
    }

    public function create(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'email' => 'required|email|max:255',
            'numero' => 'required|string|regex:/^9\d{8}$/',  // ✅ Formato Perú
            'mensaje' => 'required|string|max:1050',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        // ✅ SANITIZAR ANTES DE GUARDAR
        $contacto = Contactanos::create([
            'nombre' => htmlspecialchars($request->nombre, ENT_QUOTES, 'UTF-8'),
            'email' => $request->email,
            'numero' => $request->numero,
            'mensaje' => htmlspecialchars($request->mensaje, ENT_QUOTES, 'UTF-8'),
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Contacto guardado exitosamente',
            'data' => new ContactanosResource($contacto)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $contacto = Contactanos::find($id);

        if (!$contacto) {
            return response()->json(['error' => 'Contacto no encontrado'], 404);
        }

        $validated = $request->validate([
            'estado' => 'required|boolean',
        ]);

        // ✅ VALIDAR: Solo admin puede actualizar
        $currentUser = Auth::user();
        $currentEmpleado = Empleado::where('id_user', $currentUser->id)->first();
        
        if (!$currentEmpleado || 
            strtolower($currentEmpleado->rol->nombre) !== 'administrador') {
            return response()->json([
                'status' => 403,
                'message' => 'No autorizado'
            ], 403);
        }

        $contacto->update($validated);

        return response()->json([
            'message' => 'Estado actualizado exitosamente',
            'data' => new ContactanosResource($contacto)
        ], 200);
    }
}
```

---

## 4. VALIDACIONES MEJORADAS

### ReclamacionesController.php (Mejorado)
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\libroreclamacion;
use App\Http\Resources\ReclamacionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReclamacionesController extends Controller
{
    public function create(Request $request)
    {
        // ✅ VALIDACIONES MEJORADAS
        $validated = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'  // ✅ Solo letras
            ],
            'apellido' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'documento' => [
                'required',
                'string',
                'in:DNI,Pasaporte,Carnet'  // ✅ Valores específicos
            ],
            'numeroDocumento' => [
                'required',
                'string',
                'regex:/^\d{8,10}$/'  // ✅ Solo números
            ],
            'email' => [
                'required',
                'email:rfc,dns',  // ✅ Validación fuerte de email
                'max:100'
            ],
            'celular' => [
                'required',
                'string',
                'regex:/^(51)?\s?9\d{8}$/'  // ✅ Formato Perú: 9XXXXXXXX o +51 9XXXXXXXX
            ],
            'direccion' => [
                'required',
                'string',
                'max:250',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-,\.]+$/'  // ✅ No caracteres especiales peligrosos
            ],
            'distrito' => [
                'required',
                'string',
                'max:100'
            ],
            'ciudad' => [
                'required',
                'string',
                'max:100'
            ],
            'tipoReclamo' => [
                'required',
                'string',
                'in:PRODUCTO,SERVICIO,ATENCION'  // ✅ Valores específicos
            ],
            'id_servicio' => [
                'required',
                'integer',
                'in:1,2,3,4'  // ✅ IDs específicos
            ],
            'reclamoPerson' => [
                'required',
                'string',
                'max:1050'
            ],
            'checkReclamoForm' => [
                'required',
                'boolean'
            ],
            'aceptaPoliticaPrivacidad' => [
                'required',
                'boolean',
                'in:1'  // ✅ Debe aceptar
            ],
            'fechaIncidente' => [
                'required',
                'date_format:Y-m-d',
                'before:today',  // ✅ No futuras
                'after:1900-01-01'
            ],
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        // ✅ SANITIZAR ANTES DE GUARDAR
        $reclamacion = libroReclamacion::create([
            'nombre' => htmlspecialchars($request->nombre, ENT_QUOTES, 'UTF-8'),
            'apellido' => htmlspecialchars($request->apellido, ENT_QUOTES, 'UTF-8'),
            'documento' => $request->documento,
            'numeroDocumento' => $request->numeroDocumento,
            'email' => $request->email,
            'celular' => $request->celular,
            'direccion' => htmlspecialchars($request->direccion, ENT_QUOTES, 'UTF-8'),
            'distrito' => htmlspecialchars($request->distrito, ENT_QUOTES, 'UTF-8'),
            'ciudad' => htmlspecialchars($request->ciudad, ENT_QUOTES, 'UTF-8'),
            'tipoReclamo' => $request->tipoReclamo,
            'id_servicio' => $request->id_servicio,
            'reclamoPerson' => htmlspecialchars($request->reclamoPerson, ENT_QUOTES, 'UTF-8'),
            'checkReclamoForm' => $request->checkReclamoForm,
            'aceptaPoliticaPrivacidad' => $request->aceptaPoliticaPrivacidad,
            'fechaIncidente' => $request->fechaIncidente,
            'fechaReclamo' => now(),
            'estadoReclamo' => 'PENDIENTE',
        ]);

        return response()->json([
            'message' => 'Reclamación guardada exitosamente',
            'data' => new ReclamacionResource($reclamacion),
        ], 201);
    }
}
```

### CardController.php (Mejorado)
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CardResource;
use App\Http\Resources\CardCollection;
use App\Models\Card;

class CardController extends Controller
{
    public function index_public()
    {
        try {
            $cards = Card::with(['blog.head'])
                ->where('estado_publicacion', true)
                ->orderBy('id_card', 'asc')
                ->get();
            
            // ✅ USAR RESOURCE
            return response()->json([
                'data' => CardResource::collection($cards)
            ], 200);
        } catch (\Exception $ex) {
            Log::error('Error fetching public cards', ['error' => $ex->getMessage()]);
            
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor"
            ], 500);
        }
    }

    public function index()
    {
        try {
            $cards = Card::with('blog.head')
                ->orderBy('id_card', 'asc')
                ->get();

            // ✅ USAR RESOURCE
            return response()->json([
                'data' => CardResource::collection($cards)
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor"
            ], 500);
        }
    }
}
```

---

## RESUMEN DE CAMBIOS

| Archivo | Cambio |
|---------|--------|
| `Empleado.php` | Agregar `$hidden` array |
| `User.php` | Validar que tenga password en `$hidden` ✅ (OK) |
| `Card.php` | Agregar `$hidden` array |
| `ContactanosResource.php` | Crear resource (NEW) |
| `UserResource.php` | Crear resource (NEW) |
| `CardResource.php` | Crear resource (NEW) |
| `EmpleadoResource.php` | Mejorar y agregar checks de admin |
| `AuthController.php` | Usar Resources en login/register |
| `EmpleadoController.php` | Agregar IDOR check + usar Resources |
| `ContactanosController.php` | Usar Resources + Sanitizar inputs |
| `CardController.php` | Usar Resources |
| `ReclamacionesController.php` | Mejorar validaciones + Sanitizar |

