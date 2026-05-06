# Análisis de Seguridad - Digimedia Backend

**Fecha de Auditoría:** 21 de abril de 2026  
**Estado:** ⚠️ CRÍTICO - Múltiples vulnerabilidades encontradas

---

## 1. DATOS SENSIBLES EXPUESTOS EN RESPUESTAS API

### 🔴 CRÍTICO: Contraseñas expuestas indirectamente en Login

**Archivo:** [app/Http/Controllers/Api/AuthController.php](app/Http/Controllers/Api/AuthController.php#L130-L160)  
**Línea:** 130-160

**Problema:**
```php
return response()->json([
    'status'   => 'success',
    'user'     => $user,  // ⚠️ EXPONE el modelo User completo
    'empleado' => new EmpleadoResource($empleado),
    'rol'      => $rol->nombre,
    'permisos' => $permisos,
    'token'    => $token,
]);
```

**Datos Sensibles Expuesto:**
- `password_hash` (aunque está en `$hidden`, se serializa si hay relaciones que lo cargan)
- ID interno de usuario
- Email sin encriptación
- Estructura completa de base de datos

**Riesgo:** Un atacante obtiene credenciales hasheadas que podrían ser objetivo de fuerza bruta

**Fix Rápido:**
```php
// Usar Resource en lugar de modelo directo
use App\Http\Resources\UserResource;

return response()->json([
    'status'   => 'success',
    'user'     => new UserResource($user),  // ✅ Controlado
    'empleado' => new EmpleadoResource($empleado),
    // ... resto del código
]);
```

---

### 🔴 CRÍTICO: Modelo Empleado SIN atributos hidden

**Archivo:** [app/Models/Empleado.php](app/Models/Empleado.php#L1-L40)  
**Línea:** 1-40

**Problema:**
```php
class Empleado extends Model {
    // ❌ NO HAY protected $hidden array
    protected $fillable = [
        'nombre', 'apellido', 'email', 'dni', 'telefono',
        'imagen_perfil', 'imagen_perfil_url',
        'id_user', 'id_rol', 'id_subtipo_admin'
    ];
    // Falta: protected $hidden = ['created_at', 'updated_at'];
}
```

**Datos Expuestos en Endpoints:**
- `id_user` (Foreign Key - expone relación con tabla users)
- `id_subtipo_admin` (permite inferir estructura jerárquica)
- Timestamps (fecha de creación/actualización de empleados)

**Controllers Afectados:**
- `GET /api/empleados` - [EmpleadoController.php L100-150](app/Http/Controllers/Api/EmpleadoController.php#L100-L150)
- `GET /api/empleados/{id}` - Retorna modelo sin transformación
- `POST /api/register` - Devuelve Empleado directamente

**Riesgo:** Information Disclosure - mapeo de estructura interna

**Fix Rápido:**
```php
class Empleado extends Model {
    protected $hidden = [
        'id_user',
        'id_subtipo_admin',
        'created_at',
        'updated_at'
    ];
}
```

---

### 🔴 CRÍTICO: Controllers devuelven modelos directamente sin Resources

**Archivos y Líneas Afectadas:**

| Controller | Línea | Método | Problema |
|---|---|---|---|
| [CardController.php](app/Http/Controllers/Api/CardController.php#L41) | 41 | `index_public()` | `return response()->json($cards, 200)` - Expone modelos sin filtrar |
| [CardController.php](app/Http/Controllers/Api/CardController.php#L57) | 57 | `index()` | Mismo problema |
| [ModalesController.php](app/Http/Controllers/Api/ModalesController.php#L20) | 20 | `get()` | Devuelve modalservicios directamente |
| [ContactanosController.php](app/Http/Controllers/Api/ContactanosController.php#L17) | 17 | `get()` | Expone modelo Contactanos completo con teléfono/email |
| [ContactanosController.php](app/Http/Controllers/Api/ContactanosController.php#L23) | 23 | `getById()` | Mismo modelo sin filtrar |
| [ModalMailController.php](app/Http/Controllers/Api/ModalMailController.php#L43) | 43 | `sendMail()` | Retorna `$modal_mail` con número de intentos internos |
| [ModalWatController.php](app/Http/Controllers/Api/ModalWatController.php#L78) | 78 | `cambiarEstado()` | Expone contador de intentos y flags internos |

**Datos Sensibles Expuestos:**

- **Contactanos:** 
  - Email completo (público para contactos spam)
  - Teléfono completo (información personal)
  - Mensajes sin filtrar (pueden contener info sensible)
  
- **Modales:**
  - `intentos` (contador de reintentos - puede revelar patrones de ataque)
  - `puede_reintentar` (estado interno)
  - `error` (mensajes de error sin sanitizar)

- **Tarjetas/Cards:**
  - `id_empleado` (relación con personal)
  - Estado de publicación interno

**Fix Rápido:**
```php
// CardController.php L41
public function index_public() {
    $cards = Card::with(['blog.head'])->where('estado_publicacion', true)->get();
    return response()->json(CardResource::collection($cards), 200); // ✅ Usa Resource
}

// ContactanosController.php L17
public function get(Request $request) {
    $contactos = Contactanos::paginate(4);
    return response()->json(ContactanosResource::collection($contactos), 200); // ✅ Con Resource
}
```

---

### 🟠 ALTO: Timestamps expuestos en modelos

**Archivo:** Múltiples modelos

**Problema:**
```php
// BlogHead.php, BlogBody.php, etc.
public $timestamps = false;  // ✅ BIEN - no expone

// PERO algunos sí exponen:
// Empleado.php L6: public $timestamps = true;
// Card.php: por defecto timestamps = true
```

**Modelos sin `$hidden` para timestamps:**
- [Blog.php](app/Models/Blog.php) - Sin protección de `created_at`, `updated_at`
- [Card.php](app/Models/Card.php) - Sin `$hidden`
- [Empleado.php](app/Models/Empleado.php) - Expone `created_at` (hora de registro del empleado)

**Riesgo:** Timing attacks - Se puede inferir cuándo fue registrado cada recurso

---

## 2. VULNERABILIDADES EN MASS ASSIGNMENT

### 🔴 CRÍTICO: $request->all() usado directamente en create/update

**Archivos Afectados:**

| Archivo | Línea | Método | Código |
|---|---|---|---|
| [ModalesController.php](app/Http/Controllers/Api/ModalesController.php#L49) | 49 | `create()` | `modalservicios::create($request->all())` |
| [BlogHeadController.php](app/Http/Controllers/Api/BlogHeadController.php#L37) | 37 | `create()` | `BlogHead::create($request->all())` |
| [BlogHeadController.php](app/Http/Controllers/Api/BlogHeadController.php#L86) | 86 | `update()` | `$blogHead->update($request->all())` |
| [BlogBodyController.php](app/Http/Controllers/Api/BlogBodyController.php) | 54 | `create()` | `BlogBody::create($request->all())` |
| [BlogFooterController.php](app/Http/Controllers/Api/BlogFooterController.php) | 40 | `create()` | `BlogFooter::create($request->all())` |
| [ReclamacionesController.php](app/Http/Controllers/Api/ReclamacionesController.php#L59) | 59 | `create()` | `libroReclamacion::create($request->all())` |
| [TarjetaController.php](app/Http/Controllers/Api/TarjetaController.php) | 51 | `create()` | `Tarjeta::create($request->all())` |

**Riesgo - Mass Assignment Vulnerability:**

Ejemplo de ataque:
```bash
# Un atacante envía:
POST /api/blog_head
{
  "titulo": "Legítimo",
  "created_by": 999,      # ❌ Campo no en formulario
  "updated_by": 999,      # ❌ Campo no esperado
  "id_rol_admin": 1       # ❌ Puede modificar campos internos
}

# El modelo crea el recurso con esos campos si no están en $guarded
```

**Fix Rápido - Validar antes de crear:**
```php
// ModalesController.php L49
$validated = $request->validate([
    'nombre' => 'required|string|max:100',
    'telefono' => 'required|string|max:9',
    'correo' => 'required|email',
    'id_servicio' => 'required|integer|in:1,2,3,4',
    'estado' => 'required|boolean',
    // ❌ SIN created_at, updated_by, etc.
]);

$modal_servicio = modalservicios::create($validated); // ✅ Solo campos permitidos
```

---

## 3. ENDPOINTS CON ISSUES DE AUTORIZACIÓN

### 🔴 CRÍTICO: Sin validación de propiedad de recurso (IDOR)

**Archivo:** [app/Http/Controllers/Api/EmpleadoController.php](app/Http/Controllers/Api/EmpleadoController.php#L50-L80)  
**Línea:** 50-80

**Problema:**
```php
public function getById($id) {
    $empleado = Empleado::with('rol')->where('id_empleado', $id)->first();
    
    if (!$empleado) {
        return response()->json(["status" => 404, "message" => "Empleado no encontrado"]);
    }
    
    // ❌ NO VALIDA que Auth::user() sea el empleado solicitado
    // Un atacante puede hacer:
    // GET /api/empleados/999 y obtener datos de otro usuario
}
```

**Endpoint Vulnerable:**
- `GET /api/empleados/{id}` - Retorna datos de CUALQUIER empleado

**Ataque Posible:**
```bash
# Usuario con ID 50 ejecuta:
GET /api/empleados/51
# Obtiene: email, telefono, id_rol de otro empleado
```

**Fix Rápido:**
```php
public function getById($id) {
    $empleado = Empleado::with('rol')->where('id_empleado', $id)->first();
    
    if (!$empleado) {
        return response()->json(["status" => 404], 404);
    }
    
    // ✅ Validar autorización
    $currentUser = Auth::user();
    $currentEmpleado = Empleado::where('id_user', $currentUser->id)->first();
    
    // Solo permite si es admin O es el mismo empleado
    if (!$currentEmpleado->isAdmin() && $currentEmpleado->id_empleado !== $id) {
        return response()->json(["status" => 403, "message" => "No autorizado"], 403);
    }
    
    return response()->json(["status" => 200, "data" => $empleado]);
}
```

---

### 🟠 ALTO: Endpoints que usan `permission` middleware pero sin validar ID del recurso

**Archivo:** [routes/api.php](routes/api.php)

**Rutas Afectadas:**
```php
Route::middleware('permission:editar-contactos')->put('/contactanos/{id}', 
    [ContactanosController::class, "update"]);
// ✅ Tiene permission pero NO valida que sea dueño del contacto

Route::middleware('permission:editar-reclamaciones')->put('/reclamaciones/{id}', 
    [ReclamacionesController::class, "update"]);
// ✅ Tiene permission pero NO valida posesión
```

**Problema:** Un usuario con permiso `editar-contactos` puede editar CUALQUIER contacto de CUALQUIER usuario

**Verificar en:** [ContactanosController.php](app/Http/Controllers/Api/ContactanosController.php#L51-L72)

```php
public function update(Request $request, $id) {
    $contacto = Contactanos::find($id);
    
    if (!$contacto) {
        return response()->json(['error' => 'Contacto no encontrado'], 404);
    }
    
    // ❌ NO VALIDA: ¿Es este contacto del usuario autenticado?
    
    $contacto->update(['estado' => $request->estado]);
    return response()->json(['data' => $contacto], 200);
}
```

**Fix:** Agregar validación de propiedad en controller

---

### 🟠 ALTO: Endpoint público permite acceso a blogs sin autenticación

**Archivo:** [routes/api.php](routes/api.php#L47)

```php
// ❌ Público - sin autenticación requerida
Route::get('/blogs/{id}', [BlogController::class, "show"]);

// ✅ Pero: ¿Qué pasa si el blog_head tiene información sensible?
// ✅ ¿Se devuelve el modelo completo o usa Resource?
```

**Verificar:** Que la respuesta esté filtrada correctamente con Resources

---

## 4. VALIDACIONES FALTANTES O INADECUADAS

### 🟠 ALTO: Validación de longitud/tipo inadecuada

**Archivo:** [app/Http/Controllers/Api/ReclamacionesController.php](app/Http/Controllers/Api/ReclamacionesController.php#L36-L45)  
**Línea:** 36-45

**Problemas:**
```php
$validated = Validator::make($request->all(), [
    'nombre' => 'required|string|max:100',        // ✅ OK
    'documento' => 'required|string|max:100',     // ❌ ¿Por qué string? Debería ser validación de documento
    'numeroDocumento' => 'required|string|max:100', // ❌ Sin validación de formato de DNI/Pasaporte
    'email' => 'required|email|max:100',          // ✅ OK
    'celular' => 'required|string|max:20',        // ❌ Sin validación de formato telefónico
    'direccion' => 'required|string|max:250',     // ✅ OK
    'tipoReclamo' => 'required|string|max:20',    // ❌ Debería ser `in:` para valores específicos
    'reclamoPerson' => 'required|string|max:1050', // ✅ OK
    'fechaIncidente' => 'required|date',          // ✅ OK
]);
```

**Riesgos:**
- Inyección de SQL en campos string sin sanitizar
- Bypass de validaciones
- Datos malformados en base de datos

**Fix Rápido:**
```php
$validated = Validator::make($request->all(), [
    'nombre' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
    'documento' => 'required|in:DNI,Pasaporte,Carnet',  // ✅ Valores específicos
    'numeroDocumento' => 'required|string|regex:/^\d{8,10}$/',  // ✅ Solo números
    'celular' => 'required|regex:/^9\d{8}$/',  // ✅ Formato Perú
    'tipoReclamo' => 'required|in:PRODUCTO,SERVICIO,ATENCION',  // ✅ Valores específicos
    'fechaIncidente' => 'required|date|before:today', // ✅ No futuras
]);
```

---

### 🟠 ALTO: Validación de email en campos de contacto

**Archivo:** [app/Http/Controllers/Api/ContactanosController.php](app/Http/Controllers/Api/ContactanosController.php#L20-L30)  
**Línea:** 20-30

```php
$validated = Validator::make($request->all(), [
    'nombre' => 'required|string|max:255',
    'email' => 'required|email|max:255',          // ❌ No valida disposable emails
    'numero' => 'required|string|max:20',         // ❌ Sin validación de formato
    'mensaje' => 'required|string|max:1050',      // ❌ Sin protección XSS
]);
```

**Riesgo:** Spam, inyección de XSS en mensajes

---

## 5. CONFIGURACIÓN INSEGURA

### 🔴 CRÍTICO: Credenciales expuestas en .env.example

**Archivo:** [.env.example](.env.example)  
**Línea:** 1-50

```php
APP_DEBUG=true                          # ⚠️ EXPONE stack traces

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=laravel_digimedia
DB_USERNAME=root
DB_PASSWORD=atlantis                    # ⚠️ Contraseña visible en repo

CACHE_STORE=database
CACHE_PREFIX=
```

**Riesgos:**
1. Si `.env.example` está en control de versiones (GitHub), todo atacante ve las credenciales
2. `APP_DEBUG=true` expone stack traces completos con rutas internas
3. Contraseña default de database accesible a todo programador

**Fix Rápido:**
```bash
# 1. NO HACER COMMIT de .env.example con credenciales reales
# 2. En producción SIEMPRE:
APP_DEBUG=false

# 3. Credenciales en variables de entorno del servidor
DB_PASSWORD=<USAR_VARIABLE_ENV>

# 4. Crear .env.example SEGURO:
DB_PASSWORD=your_secure_password_here
```

---

### 🔴 CRÍTICO: Cloudinary API Secret expuesto en código

**Archivo:** [app/Http/Controllers/Api/EmpleadoController.php](app/Http/Controllers/Api/EmpleadoController.php#L370-L385)  
**Línea:** 370-385

```php
public function generateUploadSignature(Request $request, $id) {
    // ...
    $signatureString = '';
    
    foreach ($params as $key => $value) {
        $signatureString .= $key . '=' . $value;
    }
    
    $signatureString .= env('CLOUDINARY_SECRET');  // ⚠️ SE USA EN CÓDIGO
    
    $signature = hash('sha256', $signatureString);
    
    return response()->json([
        'signature' => $signature,
        'api_key' => env('CLOUDINARY_API_KEY'),   // ⚠️ EXPUESTA EN RESPUESTA
        // ...
    ]);
}
```

**Riesgo:** Exposición de API Key público a cliente

**Fix Rápido:**
```php
// ✅ NUNCA exponer secrets en respuesta
return response()->json([
    'signature' => $signature,
    'cloud_name' => config('cloudinary.cloud_name'),  // ✅ Solo público
    // 'api_key' => NO INCLUIR - Es secreto
]);
```

---

### 🟠 ALTO: APP_DEBUG=true expone información sensible

**Archivo:** [.env.example](. env.example#L11)  
**Línea:** 11

```php
APP_DEBUG=true  # En desarrollo está bien, PERO:
```

**En [AuthController.php](app/Http/Controllers/Api/AuthController.php#L148):**
```php
return response()->json([
    'status' => 'error',
    'message' => 'Ocurrió un error en el servidor',
    'error' => config('app.debug') ? $e->getMessage() : null  // ⚠️ EXPONE si debug=true
], 500);
```

**Problema:** En producción si alguien deja `APP_DEBUG=true`, todos los errores quedan expuestos

**Fix:**
```php
// Mejor: NUNCA exponer errores en producción
'error' => null  // Siempre null en responses

// Log el error internamente
Log::error('Auth error', [
    'email' => $request->email,
    'exception' => $e->getMessage()
]);
```

---

## 6. PROBLEMAS DE VALIDACIÓN Y SANITIZACIÓN

### 🟠 ALTO: Mensaje de error no sanitizado en ModalMailController

**Archivo:** [app/Http/Controllers/Api/ModalMailController.php](app/Http/Controllers/Api/ModalMailController.php#L50-L62)  
**Línea:** 50-62

```php
public function reportarError(Request $request, $id) {
    $validator = Validator::make($request->all(), [
        'error' => 'required|string|max:500',  // ❌ Sin sanitización de XSS
    ]);
    
    $modal_mail->update([
        'estado' => 1,
        'error' => $request->error,  // ⚠️ Se guarda tal cual
    ]);
}
```

**Riesgo:** Stored XSS si se devuelve el error en una interfaz web

**Fix:**
```php
$validator = Validator::make($request->all(), [
    'error' => 'required|string|max:500',
]);

// Sanitizar antes de guardar
$errorMessage = htmlspecialchars($request->error, ENT_QUOTES, 'UTF-8');

$modal_mail->update([
    'error' => $errorMessage,
]);
```

---

### 🟠 ALTO: DNI sin validación de formato

**Archivo:** [app/Http/Controllers/Api/AuthController.php](app/Http/Controllers/Api/AuthController.php#L28-L32)  
**Línea:** 28-32

```php
$validator = Validator::make($request->all(), [
    'dni' => 'required|string|max:20|unique:empleados',  // ❌ Sin formato específico
]);
```

**Problema:** Acepta cualquier string de 20 caracteres como DNI válido

**Fix:**
```php
'dni' => 'required|string|regex:/^\d{8}$/|unique:empleados',  // Perú: 8 dígitos
```

---

## 7. PROBLEMAS DE LOGGING

### 🟠 MEDIO: Logging de datos sensibles

**Archivo:** [app/Http/Controllers/Api/EmpleadoController.php](app/Http/Controllers/Api/EmpleadoController.php#L30-L45)  
**Línea:** 30-45

```php
Log::warning("Intento no autorizado de modificar empleado restringido", [
    'target_id' => $id,
    'target_email' => $empleado->email,  // ⚠️ Email en logs
    'user_id' => Auth::id(),
    'user_email' => Auth::user()->email  // ⚠️ Email en logs
]);
```

**Riesgo:** Logs no encriptados pueden exponer información sensible

**Fix:**
```php
Log::warning("Unauthorized employee modification attempt", [
    'target_id' => $id,
    'target_email' => str_repeat('*', strlen($empleado->email) - 4) . 
                      substr($empleado->email, -4),  // ✅ Parcialmente oculto
    'user_id' => Auth::id(),
    // 'user_email' => NO INCLUIR
]);
```

---

## RESUMEN DE VULNERABILIDADES POR SEVERIDAD

### 🔴 CRÍTICO (Requiere fix inmediato)
- [ ] User/Empleado devueltos sin usar Resources - Líneas: AuthController 60-71, 130-160
- [ ] Modelo Empleado sin $hidden attributes - Empleado.php
- [ ] $request->all() en creates/updates - 7 controllers
- [ ] IDOR en getById endpoints - EmpleadoController L50
- [ ] Credenciales en .env.example - .env.example
- [ ] Cloudinary API expuesto - EmpleadoController L363-380

### 🟠 ALTO (Importante corregir)
- [ ] Controllers retornan modelos directamente - CardController, ModalesController, ContactanosController (5+ endpoints)
- [ ] Sin validación de propiedad en update endpoints
- [ ] Validaciones inadecuadas en formularios - ReclamacionesController, ContactanosController
- [ ] Timestamps expuestos - Múltiples modelos
- [ ] Endpoint público sin filtrado - blogs/{id}
- [ ] Mensaje de error no sanitizado - ModalMailController L50
- [ ] DNI sin validación de formato - AuthController L31

### 🟡 MEDIO
- [ ] APP_DEBUG=true en .env.example
- [ ] Logging de datos sensibles - EmpleadoController L40-45

---

## PLAN DE REMEDIACIÓN (Prioridad)

### URGENTE (Hoy)
1. Crear Resources para todos los modelos que se devuelven en JSON
2. Agregar $hidden en Empleado model
3. Reemplazar $request->all() con validated data
4. Agregar validaciones de autorización (IDOR check)
5. Remover credenciales de .env.example

### CORTO PLAZO (Esta semana)
6. Mejorar validaciones en controllers
7. Sanitizar inputs/outputs
8. Agregar sanitización XSS
9. Revisar logging

### MEDIANO PLAZO
10. Auditoría de seguridad completa en middlewares
11. Penetration testing
12. Implementar rate limiting
13. CORS configuration review

---

## ARCHIVOS A REVISAR/MODIFICAR

**Priority 1:**
- `app/Http/Controllers/Api/AuthController.php`
- `app/Models/Empleado.php`
- `app/Http/Controllers/Api/EmpleadoController.php`
- `app/Http/Controllers/Api/CardController.php`
- `app/Http/Controllers/Api/ContactanosController.php`
- `app/Http/Controllers/Api/ModalesController.php`
- `.env.example`

**Priority 2:**
- `app/Http/Controllers/Api/BlogBodyController.php`
- `app/Http/Controllers/Api/BlogHeadController.php`
- `app/Http/Controllers/Api/ReclamacionesController.php`
- `routes/api.php` (revisar middleware)

**Priority 3:**
- `app/Http/Controllers/Api/ModalMailController.php`
- `app/Http/Controllers/Api/ModalWatController.php`

---

## CHECKLIST DE VALIDACIÓN

- [ ] ¿Todos los endpoints devuelven mediante Resources?
- [ ] ¿Todos los modelos tienen $hidden correctamente configurado?
- [ ] ¿Todos los create/update usan datos validados (no $request->all())?
- [ ] ¿Todos los endpoints verifican autorización del recurso (IDOR check)?
- [ ] ¿.env.example NO contiene credenciales reales?
- [ ] ¿APP_DEBUG=false en producción?
- [ ] ¿Cloudinary API keys NO se exponen al cliente?
- [ ] ¿Validaciones incluyen regex/formato específico?
- [ ] ¿Inputs se sanitizan contra XSS?
- [ ] ¿Logs NO contienen datos sensibles?

