<?php

/**
 * @OA\Info(
 *    title="Digimedia Backend API",
 *    version="1.0.0",
 *    description="API REST para el sistema de gestión de blogs, campañas WhatsApp y administración de empleados",
 *    contact=@OA\Contact(
 *       email="soporte@digimedia.com",
 *       name="Digimedia Support"
 *    ),
 *    license=@OA\License(
 *       name="MIT"
 *    )
 * )
 *
 * @OA\Server(
 *    url="http://localhost:8000/api",
 *    description="Development Server"
 * )
 *
 * @OA\Server(
 *    url="https://api.digimedia.com",
 *    description="Production Server"
 * )
 *
 * @OA\SecurityScheme(
 *    type="http",
 *    description="Login with username and password to get the authentication token",
 *    name="Password-based Login",
 *    in="header",
 *    scheme="bearer",
 *    securityScheme="sanctum"
 * )
 *
 * @OA\SecurityScheme(
 *    type="apiKey",
 *    name="X-API-Key",
 *    in="header",
 *    securityScheme="api_key",
 *    description="API Key for template endpoints"
 * )
 *
 * @OA\Schema(
 *    schema="UserResponse",
 *    type="object",
 *    title="User Response",
 *    @OA\Property(property="id", type="integer", example=1),
 *    @OA\Property(property="name", type="string", example="Juan Pérez"),
 *    @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
 *    @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-19T10:00:00Z")
 * )
 *
 * @OA\Schema(
 *    schema="EmpleadoResponse",
 *    type="object",
 *    title="Empleado Response",
 *    @OA\Property(property="id_empleado", type="integer", example=1),
 *    @OA\Property(property="nombre", type="string", example="Juan"),
 *    @OA\Property(property="apellido", type="string", example="Pérez"),
 *    @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
 *    @OA\Property(property="dni", type="string", example="12345678"),
 *    @OA\Property(property="telefono", type="string", nullable=true, example="987654321"),
 *    @OA\Property(property="id_user", type="integer", example=1),
 *    @OA\Property(property="id_rol", type="integer", example=2)
 * )
 *
 * @OA\Schema(
 *    schema="RegisterRequest",
 *    type="object",
 *    title="Register Request",
 *    required={"nombre", "apellido", "email", "dni", "id_rol"},
 *    @OA\Property(property="nombre", type="string", maxLength=255, example="Juan", description="Nombre del usuario"),
 *    @OA\Property(property="apellido", type="string", maxLength=255, example="Pérez", description="Apellido del usuario"),
 *    @OA\Property(property="email", type="string", format="email", maxLength=255, example="juan@example.com", description="Email único del usuario"),
 *    @OA\Property(property="dni", type="string", maxLength=20, example="12345678", description="DNI único del usuario"),
 *    @OA\Property(property="telefono", type="string", nullable=true, maxLength=20, example="987654321", description="Teléfono del usuario (opcional)"),
 *    @OA\Property(property="id_rol", type="integer", example=2, description="ID del rol a asignar (debe existir en la tabla roles)")
 * )
 *
 * @OA\Schema(
 *    schema="RegisterSuccessResponse",
 *    type="object",
 *    title="Register Success Response",
 *    @OA\Property(property="status", type="string", example="success"),
 *    @OA\Property(property="message", type="string", example="Usuario registrado exitosamente"),
 *    @OA\Property(property="user", ref="#/components/schemas/UserResponse"),
 *    @OA\Property(property="empleado", ref="#/components/schemas/EmpleadoResponse"),
 *    @OA\Property(property="rol", type="string", example="administrador", description="Nombre del rol asignado"),
 *    @OA\Property(property="token", type="string", example="1|XYZabc123...", description="Token de autenticación Sanctum para usar en requests autenticados")
 * )
 *
 * @OA\Schema(
 *    schema="ValidationErrorResponse",
 *    type="object",
 *    title="Validation Error Response",
 *    @OA\Property(property="errors", type="object", description="Errores de validación por campo",
 *       @OA\Property(property="nombre", type="array", @OA\Items(type="string", example="El campo nombre es requerido")),
 *       @OA\Property(property="email", type="array", @OA\Items(type="string", example="El email ya ha sido registrado")),
 *       @OA\Property(property="dni", type="array", @OA\Items(type="string", example="El DNI ya existe"))
 *    )
 * )
 *
 * @OA\Schema(
 *    schema="ServerErrorResponse",
 *    type="object",
 *    title="Server Error Response",
 *    @OA\Property(property="status", type="string", example="error"),
 *    @OA\Property(property="message", type="string", example="Error al registrar usuario"),
 *    @OA\Property(property="error", type="string", nullable=true, example="Descripción del error (solo si debug=true)")
 * )
 *
 * @OA\Schema(
 *    schema="LoginRequest",
 *    type="object",
 *    title="Login Request",
 *    required={"email", "password"},
 *    @OA\Property(property="email", type="string", format="email", example="juan@example.com", description="Email del usuario registrado"),
 *    @OA\Property(property="password", type="string", format="password", example="mi_contraseña", description="Contraseña del usuario")
 * )
 *
 * @OA\Schema(
 *    schema="RolResponse",
 *    type="object",
 *    title="Rol Response",
 *    @OA\Property(property="id_rol", type="integer", example=1),
 *    @OA\Property(property="nombre", type="string", example="administrador"),
 *    @OA\Property(property="descripcion", type="string", example="Acceso total al sistema")
 * )
 *
 * @OA\Schema(
 *    schema="LoginSuccessResponse",
 *    type="object",
 *    title="Login Success Response",
 *    @OA\Property(property="status", type="string", example="success"),
 *    @OA\Property(property="user", ref="#/components/schemas/UserResponse"),
 *    @OA\Property(property="empleado", ref="#/components/schemas/EmpleadoResponse"),
 *    @OA\Property(property="rol", type="string", example="administrador", description="Nombre del rol del usuario"),
 *    @OA\Property(property="permisos", type="array", @OA\Items(type="string", example="ver-blogs"), description="Lista de slugs de permisos asignados al rol"),
 *    @OA\Property(property="token", type="string", example="1|XYZabc123...", description="Token de autenticación Sanctum para autorización en requests posteriores")
 * )
 *
 * @OA\Schema(
 *    schema="ErrorResponse",
 *    type="object",
 *    title="Error Response",
 *    @OA\Property(property="status", type="string", example="error"),
 *    @OA\Property(property="message", type="string", example="Error description")
 * )
 *
 * @OA\Post(
 *    path="/register",
 *    operationId="register",
 *    tags={"Authentication"},
 *    summary="Registrar nuevo usuario",
 *    description="Crea un nuevo usuario y empleado en el sistema. Asigna un rol específico y genera un token de autenticación.",
 *    @OA\RequestBody(
 *       required=true,
 *       description="Datos necesarios para registrar un nuevo usuario",
 *       @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
 *    ),
 *    @OA\Response(
 *       response=201,
 *       description="Usuario registrado exitosamente",
 *       @OA\JsonContent(ref="#/components/schemas/RegisterSuccessResponse")
 *    ),
 *    @OA\Response(
 *       response=422,
 *       description="Error de validación - Datos inválidos o duplicados",
 *       @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *    ),
 *    @OA\Response(
 *       response=500,
 *       description="Error del servidor",
 *       @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *    )
 * )
 *
 * @OA\Post(
 *    path="/login",
 *    operationId="login",
 *    tags={"Authentication"},
 *    summary="Iniciar sesión",
 *    description="Autentica un usuario con sus credenciales (email y contraseña). Retorna el datos del usuario, empleado, roles, permisos y un token Sanctum para autorización.",
 *    @OA\RequestBody(
 *       required=true,
 *       description="Credenciales de autenticación",
 *       @OA\JsonContent(ref="#/components/schemas/LoginRequest")
 *    ),
 *    @OA\Response(
 *       response=200,
 *       description="Autenticación exitosa",
 *       @OA\JsonContent(ref="#/components/schemas/LoginSuccessResponse")
 *    ),
 *    @OA\Response(
 *       response=404,
 *       description="Usuario no registrado",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="string", example="error"),
 *          @OA\Property(property="message", type="string", example="Esta cuenta no está registrada en Digimedia.")
 *       )
 *    ),
 *    @OA\Response(
 *       response=401,
 *       description="Credenciales inválidas (email o contraseña incorrectos)",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="string", example="error"),
 *          @OA\Property(property="message", type="string", example="El email o la contraseña son incorrectos.")
 *       )
 *    ),
 *    @OA\Response(
 *       response=403,
 *       description="Usuario sin rol asignado",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="string", example="error"),
 *          @OA\Property(property="message", type="string", example="El usuario no tiene un rol asignado")
 *       )
 *    ),
 *    @OA\Response(
 *       response=500,
 *       description="Error del servidor",
 *       @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *    )
 * )
 */
