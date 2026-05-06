# 📊 ANÁLISIS EXHAUSTIVO DE LA BASE DE DATOS - DigiMedia Backend

**Fecha del análisis:** 8 de abril de 2026  
**Proyecto:** DigiMedia Backend (Laravel)

---

## 📋 TABLA DE CONTENIDOS
1. [Tablas Principales](#tablas-principales)
2. [Relaciones y Foreign Keys](#relaciones-y-foreign-keys)
3. [Campos Buscados Frecuentemente](#campos-buscados-frecuentemente)
4. [Problemas N+1 Query Identificados](#problemas-n1-query-identificados)
5. [Recomendaciones de Índices](#recomendaciones-de-índices)
6. [Queries Críticas a Optimizar](#queries-críticas-a-optimizar)

---

## 📁 TABLAS PRINCIPALES

### 1. **users** (Laravel Built-in)
```
Tabla: users
Primary Key: id (bigint)
Campos:
  - id (bigint UNSIGNED PRIMARY KEY)
  - name (varchar)
  - email (varchar UNIQUE)
  - email_verified_at (timestamp nullable)
  - password (varchar)
  - remember_token (varchar nullable)
```
**Notas:** Tabla base para autenticación. Sin timestamps en User model.

---

### 2. **roles**
```
Tabla: roles
Primary Key: id_rol (bigint)
Campos:
  - id_rol (bigint UNSIGNED PRIMARY KEY)
  - nombre (varchar NOT NULL)
```
**Relaciones:**
- `1 -> N` con `empleados` (id_rol)
- `M -> M` con `permisos` (table: role_permission)

---

### 3. **permisos**
```
Tabla: permisos
Primary Key: id_permiso (bigint)
Campos:
  - id_permiso (bigint UNSIGNED PRIMARY KEY)
  - nombre (varchar UNIQUE)
  - slug (varchar UNIQUE nullable)
  - descripcion (varchar nullable)
```
**Relaciones:**
- `M -> M` con `roles` (table: role_permission)

---

### 4. **role_permission** (tabla pivote)
```
Tabla: role_permission
Primary Key: (id_rol, id_permiso)
Campos:
  - id_rol (bigint UNSIGNED FK -> roles.id_rol)
  - id_permiso (bigint UNSIGNED FK -> permisos.id_permiso)
```
**Índices:** Composite primary key

---

### 5. **empleados**
```
Tabla: empleados
Primary Key: id_empleado (bigint)
Campos:
  - id_empleado (bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT)
  - nombre (varchar)
  - apellido (varchar)
  - email (varchar UNIQUE)
  - dni (varchar UNIQUE)
  - telefono (varchar nullable)
  - imagen_perfil (varchar nullable)
  - imagen_perfil_url (varchar nullable)
  - id_user (bigint UNSIGNED NULLABLE FK onDelete cascade)
  - id_rol (bigint UNSIGNED NULLABLE FK onDelete cascade)
  - id_subtipo_admin (bigint UNSIGNED NULLABLE)
  - created_at (timestamp)
```
**Relaciones:**
- `1 <- N` con `users` (id_user)
- `N -> 1` con `roles` (id_rol)
- `N -> 1` con `subtipo_admins` (id_subtipo_admin)
- `1 -> N` con `cards` (id_empleado)
- `1 -> N` con `blogs` (id_empleado) - via auditoria
- `1 -> N` con `blog_auditoria` (id_empleado)

---

### 6. **subtipo_admins**
```
Tabla: subtipo_admins (implicit in migrations)
Primary Key: id
Campos:
  - id (bigint UNSIGNED PRIMARY KEY)
  - description (varchar)
  - hierarchy (integer)
```

---

### 7. **blogs**
```
Tabla: blogs
Primary Key: id_blog (bigint)
Campos:
  - id_blog (bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT)
  - id_blog_head (bigint UNSIGNED UNIQUE FK onDelete cascade)
  - id_blog_body (bigint UNSIGNED UNIQUE FK onDelete cascade)
  - id_blog_footer (bigint UNSIGNED UNIQUE FK onDelete cascade)
  - fecha (timestamp DEFAULT CURRENT_TIMESTAMP)
  - link (varchar) - slug/URL único [UNIQUE INDEX RECOMENDADO]
```
**Índices Existentes:**
- UNIQUE: id_blog_head, id_blog_body, id_blog_footer

**Relaciones:**
- `N -> 1` con `blog_heads` (id_blog_head)
- `N -> 1` con `blog_bodies` (id_blog_body)
- `N -> 1` con `blog_footers` (id_blog_footer)
- `1 <- 1` con `cards` (id_blog)
- `1 -> N` con `blog_auditoria` (id_blog)

---

### 8. **blog_heads**
```
Tabla: blog_heads
Primary Key: id_blog_head (bigint)
Campos:
  - id_blog_head (bigint UNSIGNED PRIMARY KEY)
  - titulo (varchar 50)
  - texto_frase (varchar 70)
  - texto_descripcion (varchar 120)
  - public_image (text)
  - url_image (text nullable)
  - alt (varchar nullable) [SEO]
  - title (varchar nullable) [SEO]
  - meta_title (varchar nullable) [SEO]
  - meta_descripcion (varchar nullable) [SEO]
```

---

### 9. **blog_bodies**
```
Tabla: blog_bodies
Primary Key: id_blog_body (bigint)
Campos:
  - id_blog_body (bigint UNSIGNED PRIMARY KEY)
  - titulo (varchar)
  - descripcion (text)
  - id_commend_tarjeta (bigint UNSIGNED UNIQUE FK nullable)
  - public_image1 (text)
  - url_image1 (text nullable)
  - public_image2 (text)
  - url_image2 (text nullable)
  - public_image3 (text nullable)
  - url_image3 (text nullable)
  - flag_galeria (boolean nullable)
  - flag_consejos (boolean nullable)
  - flag_informacion (boolean nullable)
  - service_url (varchar nullable)
  - titulo_tarjeta (varchar nullable)
```
**Relaciones:**
- `N -> 1` con `commend_tarjetas` (id_commend_tarjeta)
- `1 -> N` con `tarjetas` (id_blog_body)

---

### 10. **blog_footers**
```
Tabla: blog_footers
Primary Key: id_blog_footer (bigint)
Campos:
  - id_blog_footer (bigint UNSIGNED PRIMARY KEY)
  - titulo (varchar)
  - descripcion (text)
  - public_image1 (text nullable)
  - url_image1 (text nullable)
  - public_image2 (text nullable)
  - url_image2 (text nullable)
  - public_image3 (text nullable)
  - url_image3 (text nullable)
  - estado (varchar nullable) [possible enum]
  - palabra (varchar nullable)
  - enlace (varchar nullable)
  - meta_title (varchar nullable) [SEO]
  - meta_descripcion (varchar nullable) [SEO]
```

---

### 11. **cards**
```
Tabla: cards
Primary Key: id_card (bigint)
Campos:
  - id_card (bigint UNSIGNED PRIMARY KEY)
  - titulo (varchar)
  - descripcion (text)
  - public_image (text)
  - url_image (text nullable)
  - id_plantilla (bigint nullable) [¿FK roto?]
  - id_blog (bigint UNSIGNED UNIQUE FK onDelete cascade)
  - id_empleado (bigint UNSIGNED FK onDelete cascade)
  - estado_publicacion (boolean nullable)
  - created_at (timestamp nullable)
  - updated_at (timestamp nullable)
```
**Índices Existentes:**
- UNIQUE: id_blog
- idx_estado_publicacion [estado_publicacion]

**Relaciones:**
- `N -> 1` con `blogs` (id_blog)
- `N -> 1` con `empleados` (id_empleado)

---

### 12. **blog_auditoria**
```
Tabla: blog_auditoria
Primary Key: id_blog_auditoria (bigint)
Campos:
  - id_blog_auditoria (bigint UNSIGNED PRIMARY KEY)
  - id_blog (bigint UNSIGNED FK)
  - id_empleado (bigint UNSIGNED FK)
  - accion (enum: 'CREAR', 'ACTUALIZAR', 'ELIMINAR')
  - titulo (varchar nullable)
  - descripcion (text nullable)
  - fecha_hora (timestamp DEFAULT CURRENT_TIMESTAMP)
```
**Índices a Considerar:**
- INDEX: (id_blog, accion)
- INDEX: (id_empleado, accion)
- INDEX: (accion, fecha_hora)
- INDEX: (fecha_hora)

**Relaciones:**
- `N -> 1` con `blogs` (id_blog)
- `N -> 1` con `empleados` (id_empleado)

---

### 13. **commend_tarjetas**
```
Tabla: commend_tarjetas
Primary Key: id_commend_tarjeta (bigint)
Campos:
  - id_commend_tarjeta (bigint UNSIGNED PRIMARY KEY)
  - titulo (varchar)
  - texto1-5 (text)
```

---

### 14. **tarjetas**
```
Tabla: tarjetas
Primary Key: id_tarjeta (bigint)
Campos:
  - id_tarjeta (bigint UNSIGNED PRIMARY KEY)
  - titulo (varchar)
  - descripcion (text)
  - id_blog_body (bigint UNSIGNED FK)
  - enlace (varchar nullable)
  - palabra (varchar nullable)
```
**Relaciones:**
- `N -> 1` con `blog_bodies` (id_blog_body)

---

### 15. **servicios**
```
Tabla: servicios
Primary Key: id_servicio (bigint)
Campos:
  - id_servicio (bigint UNSIGNED PRIMARY KEY)
  - nombre (varchar 30 nullable)
  - descripcion (varchar 1000 nullable)
```
**Relaciones:**
- `1 -> N` con `modalservicios` (id_servicio)
- `1 -> N` con `campanias_whatsapp` (id_servicio)
- `1 -> N` con `plantillas_whatsapp` (id_servicio)
- `1 -> N` con `plantillas_email` (id_servicio)

---

### 16. **modalservicios**
```
Tabla: modalservicios
Primary Key: id_modalservicio (bigint)
Campos:
  - id_modalservicio (bigint UNSIGNED PRIMARY KEY)
  - nombre (varchar 100)
  - telefono (varchar 9)
  - correo (varchar 200)
  - id_servicio (bigint UNSIGNED FK onDelete cascade)
  - estado (boolean nullable DEFAULT 1)
  - fecha (timestamp DEFAULT CURRENT_TIMESTAMP)
```
**Índices Existentes:**
- INDEX: estado
- INDEX: id_servicio
- INDEX: (estado, fecha)

**Relaciones:**
- `N -> 1` con `servicios` (id_servicio)
- `1 -> N` con `modal_wats` (id_modalservicio)
- `1 -> N` con `modal_emails` (id_modalservicio)

---

### 17. **modal_wats**
```
Tabla: modal_wats
Primary Key: id_modal_wat (bigint)
Campos:
  - id_modal_wat (bigint UNSIGNED PRIMARY KEY)
  - estado (boolean DEFAULT 0)
  - error (varchar 500 nullable)
  - id_modalservicio (bigint UNSIGNED FK onDelete cascade)
  - number_message (enum: 1, 2, 3)
  - fecha (date DEFAULT NOW)
  - intentos (integer nullable)
  - campania_id (bigint UNSIGNED nullable FK)
  - puede_reintentar (boolean nullable)
```
**Índices Existentes:**
- INDEX: estado
- INDEX: id_modalservicio
- INDEX: (estado, fecha)

**Relaciones:**
- `N -> 1` con `modalservicios` (id_modalservicio)
- `N -> 1` con `campanias_whatsapp` (campania_id)

---

### 18. **modal_emails**
```
Tabla: modal_emails
Primary Key: id_modal_email (bigint)
Campos:
  - id_modal_email (bigint UNSIGNED PRIMARY KEY)
  - estado (boolean DEFAULT 0)
  - error (varchar 500 nullable)
  - id_modalservicio (bigint UNSIGNED FK onDelete cascade)
  - number_message (enum: 1, 2, 3)
  - fecha (date DEFAULT NOW)
```
**Índices Existentes:**
- INDEX: estado
- INDEX: id_modalservicio
- INDEX: (estado, fecha)

**Relaciones:**
- `N -> 1` con `modalservicios` (id_modalservicio)

---

### 19. **contactanos**
```
Tabla: contactanos
Primary Key: id_contactanos (bigint)
Campos:
  - id_contactanos (bigint UNSIGNED PRIMARY KEY)
  - nombre (varchar 250)
  - email (varchar 250)
  - numero (varchar 15)
  - mensaje (text nullable)
  - estado (boolean DEFAULT 1)
  - fecha (timestamp DEFAULT CURRENT_TIMESTAMP)
```

---

### 20. **reclamaciones** (libro_reclamacion)
```
Tabla: reclamaciones
Primary Key: id_reclamacion (bigint)
Campos:
  - id_reclamacion (bigint UNSIGNED PRIMARY KEY)
  - nombre (varchar)
  - apellido (varchar)
  - documento (varchar)
  - numeroDocumento (varchar)
  - email (varchar)
  - celular (varchar)
  - direccion (varchar)
  - distrito (varchar)
  - ciudad (varchar)
  - tipoReclamo (varchar)
  - id_servicio (bigint UNSIGNED)
  - reclamoPerson (text)
  - checkReclamoForm (boolean)
  - aceptaPoliticaPrivacidad (boolean)
  - fechaReclamo (timestamp)
  - fechaIncidente (timestamp)
  - estadoReclamo (varchar)
```

---

### 21. **campanias_whatsapp**
```
Tabla: campanias_whatsapp
Primary Key: id_campania (bigint)
Campos:
  - id_campania (bigint UNSIGNED PRIMARY KEY)
  - id_servicio (bigint UNSIGNED FK onDelete cascade)
  - user_id (bigint UNSIGNED nullable FK onDelete set null)
  - parrafo (text)
  - imagen_url (varchar 500)
  - estado (enum: 'pendiente','en_proceso','completada','cancelada','error','borrador')
  - total_destinatarios (integer DEFAULT 0)
  - envios_exitosos (integer DEFAULT 0)
  - envios_fallidos (integer DEFAULT 0)
  - envios_pendientes (integer DEFAULT 0)
  - envios_hoy (integer DEFAULT 0)
  - fecha_ultimo_envio (date nullable)
  - fecha_inicio (timestamp nullable)
  - fecha_fin (timestamp nullable)
  - pausada_fuera_horario (boolean nullable)
  - pausada_sin_conexion (boolean nullable)
  - daily_limit_fields (various)
  - created_at (timestamp)
  - updated_at (timestamp)
```
**Índices Existentes:**
- INDEX: estado
- INDEX: created_at

**Relaciones:**
- `N -> 1` con `servicios` (id_servicio)
- `N -> 1` con `users` (user_id)

---

### 22. **plantillas_whatsapp**
```
Tabla: plantillas_whatsapp
Primary Key: id_plantilla_whatsapp (bigint)
Campos:
  - id_plantilla_whatsapp (bigint UNSIGNED PRIMARY KEY)
  - id_servicio (bigint UNSIGNED FK onDelete cascade)
  - numero_plantilla (tinyint UNSIGNED)
  - nombre (varchar 100 nullable)
  - mensaje (text)
  - imagen_url (varchar nullable)
  - created_by (bigint UNSIGNED nullable FK)
  - updated_by (bigint UNSIGNED nullable FK)
  - created_at (timestamp)
  - updated_at (timestamp)
```
**Índices Existentes:**
- UNIQUE: (id_servicio, numero_plantilla)
- INDEX: id_servicio

**Relaciones:**
- `N -> 1` con `servicios` (id_servicio)
- `N -> 1` con `users` (created_by, updated_by)

---

### 23. **plantillas_email**
```
Tabla: plantillas_email
Primary Key: id_plantilla_email (bigint)
Campos:
  - id_plantilla_email (bigint UNSIGNED PRIMARY KEY)
  - id_servicio (bigint UNSIGNED FK onDelete cascade)
  - numero_plantilla (tinyint UNSIGNED)
  - nombre (varchar 100 nullable)
  - asunto (varchar 255)
  - encabezado (varchar 255)
  - imagen_url (varchar 500)
  - mensaje (text)
  - mensaje_boton (varchar 150 nullable)
  - url_boton (varchar 500 nullable)
  - footer (text nullable)
  - red_facebook (varchar 255 DEFAULT 'https://www.facebook.com/DigiMedia.Marketing1')
  - red_tiktok (varchar 255 nullable)
  - red_instagram (varchar 255 DEFAULT 'https://www.instagram.com/digimedia.pe/')
  - red_linkedin (varchar 255 nullable)
  - created_by (bigint UNSIGNED FK nullable)
  - updated_by (bigint UNSIGNED FK nullable)
  - created_at (timestamp)
  - updated_at (timestamp)
```
**Índices Existentes:**
- UNIQUE: (id_servicio, numero_plantilla)
- INDEX: id_servicio
- INDEX: updated_at

**Relaciones:**
- `N -> 1` con `servicios` (id_servicio)
- `N -> 1` con `users` (created_by, updated_by)

---

### 24. **user_action**
```
Tabla: user_action
Primary Key: id (bigint)
Campos:
  - id (bigint UNSIGNED PRIMARY KEY)
  - usuario_id (bigint UNSIGNED nullable)
  - usuario_email (varchar nullable)
  - ip (ipaddress nullable)
  - metodo (varchar)
  - url (varchar)
  - datos (text nullable)
  - user_agent (varchar nullable)
  - created_at (timestamp)
  - updated_at (timestamp)
```
**Nota:** Tabla de auditoría de acciones de usuarios, sin FK definidas.

---

## 🔗 RELACIONES Y FOREIGN KEYS

### Diagrama de Relaciones:

```
┌─────────────────────────────────────────────────────────────┐
│                        AUTENTICACIÓN                        │
├─────────────────────────────────────────────────────────────┤
│  users (id)
│    ├─→ empleados (id_user)
│    ├─→ campanias_whatsapp (user_id)
│    ├─→ plantillas_whatsapp (created_by, updated_by)
│    └─→ plantillas_email (created_by, updated_by)
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                       ROLES Y PERMISOS                      │
├─────────────────────────────────────────────────────────────┤
│  roles (id_rol)
│    ├─→ empleados (id_rol)
│    └─→ role_permission M:M
│         └─→ permisos (id_permiso)
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                       GESTIÓN EMPLEADOS                     │
├─────────────────────────────────────────────────────────────┤
│  empleados (id_empleado)
│    ├─→ users (id_user) [FK]
│    ├─→ roles (id_rol) [FK]
│    ├─→ subtipo_admins (id_subtipo_admin)
│    ├─→ cards (id_empleado) [FK]
│    └─→ blog_auditoria (id_empleado) [FK]
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                      GESTIÓN DE BLOGS                       │
├─────────────────────────────────────────────────────────────┤
│  blogs (id_blog)
│    ├─→ blog_heads (id_blog_head) [FK]
│    ├─→ blog_bodies (id_blog_body) [FK]
│    │    └─→ commend_tarjetas (id_commend_tarjeta)
│    │    └─→ tarjetas (id_blog_body)
│    ├─→ blog_footers (id_blog_footer) [FK]
│    ├─→ cards (id_blog) [1:1]
│    └─→ blog_auditoria (id_blog) [FK]
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                      SERVICIOS Y MODALES                    │
├─────────────────────────────────────────────────────────────┤
│  servicios (id_servicio)
│    ├─→ modalservicios (id_servicio) [FK]
│    │    ├─→ modal_wats (id_modalservicio) [FK]
│    │    └─→ modal_emails (id_modalservicio) [FK]
│    ├─→ campanias_whatsapp (id_servicio) [FK]
│    ├─→ plantillas_whatsapp (id_servicio) [FK]
│    └─→ plantillas_email (id_servicio) [FK]
└─────────────────────────────────────────────────────────────┘
```

### Foreign Keys Existentes:

| Tabla | Campo | Referencia | OnDelete |
|-------|-------|-----------|----------|
| empleados | id_user | users.id | cascade |
| empleados | id_rol | roles.id_rol | cascade |
| blogs | id_blog_head | blog_heads.id_blog_head | cascade |
| blogs | id_blog_body | blog_bodies.id_blog_body | cascade |
| blogs | id_blog_footer | blog_footers.id_blog_footer | cascade |
| blog_bodies | id_commend_tarjeta | commend_tarjetas.id_commend_tarjeta | cascade |
| cards | id_blog | blogs.id_blog | cascade |
| cards | id_empleado | empleados.id_empleado | cascade |
| role_permission | id_rol | roles.id_rol | cascade |
| role_permission | id_permiso | permisos.id_permiso | cascade |
| modalservicios | id_servicio | servicios.id_servicio | cascade |
| modal_wats | id_modalservicio | modalservicios.id_modalservicio | cascade |
| modal_emails | id_modalservicio | modalservicios.id_modalservicio | cascade |
| campanias_whatsapp | id_servicio | servicios.id_servicio | cascade |
| plantillas_whatsapp | id_servicio | servicios.id_servicio | cascade |
| plantillas_email | id_servicio | servicios.id_servicio | cascade |

---

## 🔍 CAMPOS BUSCADOS FRECUENTEMENTE

### En Controllers (API):

#### **Blogger & Card Management:**
```
BlogController:
  ✓ WHERE: Blog.link = ? (unique lookup)
  ✓ JOIN: blog_auditoria ON id_blog [MetricasController]
  ✓ WITH: ['card', 'body', 'head', 'footer']
  ✓ ORDER BY: created_at DESC (para "recientes")
  
CardController:
  ✓ WHERE: Card.id_empleado = ?
  ✓ WHERE: Card.id_blog = ?
  ✓ WHERE: Card.estado_publicacion = true/false
  ✓ ORDER BY: id_card ASC
  ✓ WITH: ['blog', 'blog.head', 'empleado']
  ✓ FILTER: estado_publicacion = true
  
BlogAuditoriaController:
  ✓ WHERE: accion IN ['CREAR', 'ACTUALIZAR', 'ELIMINAR']
  ✓ WHERE: fecha_hora BETWEEN ?
  ✓ GROUP BY: id_blog, fecha_hora
  ✓ ORDER BY: fecha_hora DESC
```

#### **Employee Management:**
```
EmpleadoController:
  ✓ WHERE: id_empleado = ?
  ✓ WHERE: id_rol = ?
  ✓ WHERE: nombre LIKE %?%
  ✓ WHERE: apellido LIKE %?%
  ✓ WHERE: email LIKE %?%
  ✓ WHERE: dni LIKE %?%
  ✓ WHERE: telefono LIKE %?%
  ✓ ORDER BY: id_empleado ASC (variable)
  ✓ WITH: ['rol', 'subtipoAdmin']
```

#### **Modal & WhatsApp:**
```
ModalesController:
  ✓ WHERE: id_modalservicio = ?
  ✓ WHERE: numero_message IN [1, 2, 3]
  ✓ ORDER BY: id_modalservicio ASC
  ✓ WITH: ['servicio']
  ✓ PAGINATE: 5
  
ModalWatController:
  ✓ WHERE: id_modalservicio = ? AND number_message = ?
  ✓ WHERE: estado = true/false
  
WhatsAppCampaignController:
  ✓ WHERE: id_servicio = ?
  ✓ WHERE: estado IN ['pendiente', 'en_proceso', 'completada']
  ✓ WHERE: user_id = ?
```

#### **Metrics & Analytics:**
```
MetricasController:
  ✓ WHERE: accion = 'CREAR'
  ✓ WHERE: fecha_hora BETWEEN startDate AND endDate
  ✓ JOIN: blog_auditoria AS ba ON ba.id_blog = cards.id_blog
  ✓ WHERE: cards.id_plantilla = ?
  ✓ WHERE: cards.id_empleado = ?
  ✓ WHERE: empleados.id_rol = ?
  ✓ ORDER BY: id_card, id_plantilla
  ✓ GROUP BY: YEAR(fecha_hora), MONTH(fecha_hora)
```

#### **Permissions & Roles:**
```
CheckPermission Middleware:
  ✓ WHERE: Rol.nombre = ?
  ✓ WHERE: Permiso.slug = ?
  ✓ QUERY: rol.permisos() whereRelation
  
CheckRole Middleware:
  ✓ WHERE: Rol.nombre = ?
  ✓ WHERE: Permiso.slug = ?
```

#### **Templates:**
```
PlantillasWhatsappController:
  ✓ WHERE: id_servicio = ?
  ✓ WHERE: numero_plantilla = ?
  ✓ ORDER BY: id_servicio, numero_plantilla
  ✓ WITH: ['servicio']
  
PlantillasEmailController:
  ✓ WHERE: id_servicio = ?
  ✓ WHERE: numero_plantilla = ?
  ✓ ORDER BY: id_servicio, numero_plantilla
```

---

## 🚨 PROBLEMAS N+1 QUERY IDENTIFICADOS

### **CRÍTICO - N+1 en CardController:**
```php
// CardController::get() - Línea 70
$cards = Card::with('empleado','blog')->get();
// POR CADA CARD, se ejecutan queries adicionales:
// 1. SELECT * FROM cards
// 2. SELECT * FROM empleados WHERE id_empleado = ? (N veces)
// 3. SELECT * FROM blogs WHERE id_blog = ? (N veces)
// 4. SELECT * FROM blog_heads...
// 5. SELECT * FROM blog_bodies...
// 6. SELECT * FROM blog_footers...
// TOTAL: 1 + (3 * N) queries

// SOLUCIÓN:
Card::with(['empleado', 'blog.head', 'blog.body', 'blog.footer'])->get();
```

### **CRÍTICO - N+1 en BlogController:**
```php
// BlogController::index() - Línea 21
$blogs = Blog::with('card')->get();
// PROBLEMA: Cada blog intenta cargar card, pero no de forma nested
// Mejor: WITH eager loading de relaciones anidadas
// SOLUCIÓN:
Blog::with(['card.empleado', 'head', 'body', 'footer'])->get();
```

### **CRÍTICO - N+1 en EmpleadoController:**
```php
// EmpleadoController::getAllByPage() - Línea 87
$empleados = $data->paginate($pagination);
$empleados->getCollection()->transform(function ($empleado) {
    // AQUÍ traes $empleado->rol->nombre
    // Se ejecuta una query por empleado
});

// PROBLEMA: ya haces ->with('rol') pero luego accedes en transform
// Está bien, PERO falta el subtipo_admin que cargas por defecto

// SOLUCIÓN:
$data->with(['rol', 'subtipoAdmin', 'user'])->paginate($pagination);
```

### **ALTO - N+1 en MetricasController:**
```php
// MetricasController::countBlogsByMonth() - Línea 45
$count = BlogAuditoria::where('accion', 'CREAR')
    ->whereBetween('fecha_hora', [$startDate, $endDate])
    ->count();

// LUEGO EN: 
$raw = BlogAuditoria::where('accion', 'CREAR')->get()

// DESPUÉS:
$group->first()->fecha_hora->format('Y')

// PROBLEMA: El .get() trae TODAS las auditorias sin límite
// Si hay 10k+ registros, esto es muy pesado
// SOLUCIÓN:
->select('id_blog_auditoria', 'fecha_hora', 'accion')
->get()
->groupBy(...)
```

### **ALTO - N+1 en CheckPermission Middleware:**
```php
// CheckPermission.php - Línea 29-37
foreach ($userRoles as $roleName) {
    $rol = Rol::where('nombre', $roleName)->first();  // N queries
    
    if ($rol) {
        foreach ($permissions as $permissionSlug) {
            $permiso = Permiso::where('slug', $permissionSlug)->first();  // N*M queries
            
            if ($permiso && $rol->permisos()->where(...)->exists()) {  // N*M queries adicionales
```

// SOLUCIÓN: Cachear roles y permisos, usar `whereIn()` en lugar de loops
// Use CacheService::getRoles() que ya existe
```

### **ALTO - N+1 en ModalesController:**
```php
// ModalesController::getSendModales() - Línea 25-26
$modals_mails = EmailModal::where('id_modalservicio', $id)->get();
$modal_wats = WatModal::where('id_modalservicio', $id)->get();

// LUEGO EN LOGIC:
foreach($modals_mails) {
    // Acceso a propiedades
}

// LUEGO:
$wat1 = WatModal::where('id_modalservicio', $modal_servicio->id_modalservicio)
    ->where('number_message', 1)
    ->first();  // Query individual

$wat2 = WatModal::where('id_modalservicio', $modal_servicio->id_modalservicio)
    ->where('number_message', 2)
    ->first();  // Otra query

$wat3 = WatModal::where('id_modalservicio', $modal_servicio->id_modalservicio)
    ->where('number_message', 3)
    ->first();  // Otra query más

// SOLUCIÓN:
$wats = WatModal::where('id_modalservicio', $id)->get();
$wat1 = $wats->firstWhere('number_message', 1);
$wat2 = $wats->firstWhere('number_message', 2);
$wat3 = $wats->firstWhere('number_message', 3);
```

### **MEDIO - N+1 en Middleware CheckRole:**
```php
// CheckRole.php - Línea 31
foreach ($tokenAbilities as $role) {
    $rolModel = Rol::where('nombre', $role)->first();  // N queries sin cache
```

---

## 📈 RECOMENDACIONES DE ÍNDICES

### **PRIORIDAD CRÍTICA - Crear INMEDIATAMENTE:**

#### 1. **blog_auditoria** - Para queries de métricas
```sql
-- Problema: MetricasController hace JOIN y WHERE con fecha_hora y accion constantemente
ALTER TABLE blog_auditoria 
ADD INDEX idx_accion_fecha (accion, fecha_hora),
ADD INDEX idx_id_blog_accion (id_blog, accion),
ADD INDEX idx_id_empleado_accion (id_empleado, accion),
ADD INDEX idx_fecha_hora (fecha_hora);
```

#### 2. **blogs** - Para búsquedas por link
```sql
-- Problema: BlogController busca por link que es UNIQUE pero no hay índice convencional
ALTER TABLE blogs 
ADD UNIQUE INDEX idx_link (link),
ADD INDEX idx_fecha (fecha);
```

#### 3. **cards** - Estado y filtros frecuentes
```sql
-- Problema: CardController filtra por estado_publicacion constantemente
ALTER TABLE cards 
ADD INDEX idx_estado_publicacion (estado_publicacion),
ADD INDEX idx_id_empleado (id_empleado),
ADD INDEX idx_id_blog (id_blog),
ADD INDEX idx_estado_empleado (estado_publicacion, id_empleado);
```

#### 4. **empleados** - Búsquedas y filtros
```sql
-- Problema: EmpleadoController busca por nombre, email, dni con LIKE
ALTER TABLE empleados 
ADD INDEX idx_email (email),
ADD INDEX idx_dni (dni),
ADD INDEX idx_id_rol (id_rol),
ADD INDEX idx_nombre_apellido (nombre, apellido),
ADD FULLTEXT INDEX ft_employee_search (nombre, apellido, email, dni);
```

#### 5. **roles** - Para middleware de permisos
```sql
-- Problema: CheckPermission y CheckRole buscan roles por nombre constantemente
ALTER TABLE roles 
ADD UNIQUE INDEX idx_nombre (nombre);

ALTER TABLE permisos 
ADD UNIQUE INDEX idx_slug (slug),
ADD INDEX idx_nombre (nombre);
```

#### 6. **role_permission** - Para queries de permisos
```sql
-- Problema: Queries complejas en middleware
ALTER TABLE role_permission 
ADD INDEX idx_id_rol (id_rol),
ADD INDEX idx_id_permiso (id_permiso);
```

### **PRIORIDAD ALTA - Crear en segunda fase:**

#### 7. **modalservicios** - Ya existen los índices, pero revisar:**
```sql
-- Bien configurado. Revisar:
-- Ya tienen: estado, id_servicio, (estado, fecha)
-- Considerar añadir:
ALTER TABLE modalservicios 
ADD INDEX idx_correo (correo),
ADD INDEX idx_telefono (telefono);
```

#### 8. **modal_wats** - Mejora de índices
```sql
-- Bien configurado, pero:
ALTER TABLE modal_wats 
ADD INDEX idx_campania_id (campania_id),
ADD INDEX idx_id_modalservicio_number (id_modalservicio, number_message);
```

#### 9. **campanias_whatsapp** - Para queries de campaña
```sql
-- Problema: Buscas por user_id y estado frecuentemente
ALTER TABLE campanias_whatsapp 
ADD INDEX idx_user_id (user_id),
ADD INDEX idx_estado_created (estado, created_at),
ADD INDEX idx_id_servicio (id_servicio);
```

#### 10. **plantillas_whatsapp & plantillas_email** - Mejora
```sql
-- Ya tienen unique (id_servicio, numero_plantilla)
-- Considerar:
ALTER TABLE plantillas_whatsapp 
ADD INDEX idx_created_by (created_by),
ADD INDEX idx_updated_by (updated_by);

ALTER TABLE plantillas_email 
ADD INDEX idx_created_by (created_by),
ADD INDEX idx_updated_by (updated_by);
```

### **PRIORIDAD MEDIA - Considerar opcional:**

#### 11. **blog_heads, blog_bodies, blog_footers** - SEO
```sql
-- Para futuras búsquedas SEO:
ALTER TABLE blog_heads 
ADD INDEX idx_meta_titulo (meta_title);

ALTER TABLE blog_bodies 
ADD INDEX idx_titulo (titulo);
```

#### 12. **user_action** - Tabla de auditoría
```sql
-- Para reports:
ALTER TABLE user_action 
ADD INDEX idx_usuario_id (usuario_id),
ADD INDEX idx_usuario_email (usuario_email),
ADD INDEX idx_metodo_url (metodo, url),
ADD INDEX idx_created_at (created_at);
```

---

## 🎯 QUERIES CRÍTICAS A OPTIMIZAR

### **1. MIDDLEWARE DE PERMISOS (CRÍTICO - se ejecuta en CADA request)**

#### Actual (LENTO):
```php
// CheckPermission.php
foreach ($userRoles as $roleName) {
    $rol = Rol::where('nombre', $roleName)->first();  // ❌ N queries
    if ($rol) {
        foreach ($permissions as $permissionSlug) {
            $permiso = Permiso::where('slug', $permissionSlug)->first();  // ❌ N*M queries
            if ($permiso && $rol->permisos()->where('permisos.id_permiso', $permiso->id_permiso)->exists()) {
                return $next($request);
            }
        }
    }
}
```

#### Optimizado:
```php
// Usar cache y whereIn()
$roles = Rol::whereIn('nombre', $userRoles)
    ->with('permisos')
    ->get()
    ->keyBy('nombre');

$permisos = Permiso::whereIn('slug', $permissions)
    ->get()
    ->keyBy('slug');

foreach ($userRoles as $roleName) {
    $rol = $roles->get($roleName);
    if ($rol) {
        foreach ($permissions as $permissionSlug) {
            $permiso = $permisos->get($permissionSlug);
            if ($permiso && $rol->permisos->contains('id_permiso', $permiso->id_permiso)) {
                return $next($request);
            }
        }
    }
}
```

---

### **2. METRICAS CONTROLLER (CRÍTICO - reportes pesados)**

#### Actual (PROBLEMA):
```php
// MetricasController.php línea 96
$data = BlogAuditoria::where('accion', 'CREAR')->get()  // ❌ Trae TODOS
    ->groupBy(fn($i) => Carbon::parse($i->fecha_hora)->format('Y-m'))
    ->map(fn($group) => [...])
```

#### Optimizado:
```php
$data = BlogAuditoria::where('accion', 'CREAR')
    ->select('id_blog_auditoria', 'fecha_hora')  // ✅ Solo campos necesarios
    ->whereYear('fecha_hora', '>=', 2000)  // ✅ Limitar años innecesarios
    ->get()
    ->groupBy(fn($i) => $i->fecha_hora->format('Y-m'))
    ->map(fn($group) => [
        'y' => (int) $group->first()->fecha_hora->format('Y'),
        'm' => (int) $group->first()->fecha_hora->format('m'),
        'total' => $group->count()
    ]);
```

---

### **3. CARD CONTROLLER (CRÍTICO - N+1)**

#### Actual (PROBLEMA):
```php
// CardController.php línea 38
$cards = Card::with('blog.head')->orderBy('id_card', 'asc')->get();
// ❌ Falta relación con empleado
// ❌ No carga blog.body ni blog.footer
```

#### Optimizado:
```php
$cards = Card::with([
    'empleado:id_empleado,nombre,apellido,email',
    'blog:id_blog,id_blog_head,id_blog_body,id_blog_footer,fecha',
    'blog.head:id_blog_head,titulo,meta_title',
    'blog.body:id_blog_body,titulo',
    'blog.footer:id_blog_footer,titulo'
])
->where('estado_publicacion', true)
->orderBy('id_card', 'asc')
->get();
```

---

### **4. EMPLEADO CONTROLLER (ALTO - búsquedas LIKE)**

#### Actual (PROBLEMA):
```php
// EmpleadoController.php línea 76
if(!empty($search)) {
    $data->where(function($subQuery) use ($search) {
       $subQuery->where('nombre', 'LIKE', '%' . $search . '%')
                ->orWhere('apellido', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('dni', 'LIKE', '%' . $search . '%')
                ->orWhere('telefono', 'LIKE', '%' . $search . '%'); 
    });
}
```

#### Optimizado:
```php
if(!empty($search)) {
    $searchTerm = $search . '%';  // ✅ Búsqueda por prefijo es más rápida
    $data->where(function($subQuery) use ($searchTerm) {
       $subQuery->where('nombre', 'LIKE', $searchTerm)
                ->orWhere('apellido', 'LIKE', $searchTerm)
                ->orWhere('email', 'LIKE', $searchTerm)
                ->orWhere('dni', 'LIKE', $searchTerm)
                ->orWhere('telefono', 'LIKE', $searchTerm); 
    });
}
// Alternativa: usar FULLTEXT INDEX si disponible
```

---

### **5. MODALES CONTROLLER (ALTO - múltiples queries)**

#### Actual (PROBLEMA):
```php
// ModalesController.php línea 104-119
$wat1 = WatModal::where('id_modalservicio', $modal_servicio->id_modalservicio)
    ->where('number_message', 1)
    ->first();  // ❌ Query 1

$wat2 = WatModal::where('id_modalservicio', $modal_servicio->id_modalservicio)
    ->where('number_message', 2)
    ->first();  // ❌ Query 2

$wat3 = WatModal::where('id_modalservicio', $modal_servicio->id_modalservicio)
    ->where('number_message', 3)
    ->first();  // ❌ Query 3
```

#### Optimizado:
```php
$wats = WatModal::where('id_modalservicio', $modal_servicio->id_modalservicio)
    ->get()
    ->keyBy('number_message');  // ✅ Una sola query, cachea en memoria

$wat1 = $wats->get(1);
$wat2 = $wats->get(2);
$wat3 = $wats->get(3);
```

---

### **6. BLOG AUDITORIA JOINS (ALTO - joins complejos)**

#### Actual (PROBLEMA):
```php
// MetricasController.php línea 136
$cards = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
    ->where('cards.id_plantilla', $plantilla)
    ->where('ba.accion', 'CREAR')
    ->whereBetween('ba.fecha_hora', [$startDate, $endDate])
    ->select('cards.*')
    ->get();
```

#### Optimizado:
```php
// ✅ Mejor: Usar with() + where() que join
$cards = Card::with(['blog.auditoria'])
    ->where('id_plantilla', $plantilla)
    ->get()
    ->filter(function($card) use ($startDate, $endDate) {
        return $card->blog->auditoria
            ->where('accion', 'CREAR')
            ->whereBetween('fecha_hora', [$startDate, $endDate])
            ->count() > 0;
    });

// O mejor aún: usar select explícito
$cards = Card::select('cards.*')
    ->join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
    ->where('cards.id_plantilla', $plantilla)
    ->where('ba.accion', 'CREAR')
    ->whereBetween('ba.fecha_hora', [$startDate, $endDate])
    ->distinct()
    ->get();
```

---

### **7. CONTACTANOS & RECLAMACIONES (BAJO - tablas simples)**

Sin N+1 detectados. Considerar índices para búsquedas futuras.

---

## 📊 RESUMEN EJECUTIVO

### Statisticas de Riesgo:

| Área | Riesgo | Impacto | Acción | Prioridad |
|------|--------|--------|--------|-----------|
| Middleware Permisos | N+1 en cada request | MUY ALTO | Cache + whereIn | 🔴 CRÍTICO |
| Métricas | .get() sin límite | MUY ALTO | Limitar + select | 🔴 CRÍTICO |
| Card Controller | N+1 relaciones | ALTO | Eager load correcto | 🔴 CRÍTICO |
| Empleado búsqueda | LIKE sin índice | ALTO | Index + prefix search | 🟠 ALTO |
| Modales queries | Múltiples queries | ALTO | Consolidar en 1 query | 🟠 ALTO |
| Blog Auditoria | Sin índices | ALTO | Crear 4 índices | 🟠 ALTO |
| Role Permission | Sin índices | MEDIO | Crear índices | 🟡 MEDIO |
| Template queries | Bien optimizadas | BAJO | Únicamente mantener | 🟢 BAJO |

### Índices a Crear Inmediatamente:

1. `blog_auditoria(accion, fecha_hora)` - Métricas
2. `blog_auditoria(id_blog, accion)` - Auditoría por blog
3. `blogs(link)` - Búsqueda por slug
4. `cards(estado_publicacion, id_empleado)` - Filtros card
5. `empleados(nombre, apellido)` - FULLTEXT search
6. `roles(nombre)` - UNIQUE para middleware
7. `permisos(slug)` - UNIQUE para middleware
8. `empleados(email, dni)` - UNIQUE existentes

### Queries a Refactor Inmediatamente:

1. Middleware CheckPermission - Usar cache
2. MetricasController reportes - Limitar .get()
3. CardController relaciones - Eager load completo
4. ModalesController wats - Una sola query

---

## 🔧 PRÓXIMOS PASOS

1. **Ejecutar migraciones de índices** (sin downtime)
2. **Refactorizar middleware de permisos**
3. **Optimizar queries de métricas**
4. **Implementar query logging** para detectar más N+1
5. **Establecer políticas de eager loading** en modelos

---

**Fin del Análisis**  
*Documento generado automáticamente el 8 de abril de 2026*
