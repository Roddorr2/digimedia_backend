<?php

/**
 * @OA\Schema(
 *    schema="LogoutResponse",
 *    type="object",
 *    title="Logout Response",
 *    @OA\Property(property="status", type="string", example="success"),
 *    @OA\Property(property="message", type="string", example="Sesión cerrada exitosamente")
 * )
 *
 * @OA\Schema(
 *    schema="MeResponse",
 *    type="object",
 *    title="Me Response",
 *    @OA\Property(property="user", ref="#/components/schemas/UserResponse"),
 *    @OA\Property(property="empleado", ref="#/components/schemas/EmpleadoResponse", nullable=true),
 *    @OA\Property(property="rol", type="string", example="administrador", nullable=true),
 *    @OA\Property(property="abilities", type="array", @OA\Items(type="string"), example={"administrador"}, description="Capacidades del token actual"),
 *    @OA\Property(property="permisos", type="array", @OA\Items(type="string", example="ver-blogs"), description="Lista de permisos del usuario")
 * )
 *
 * @OA\Schema(
 *    schema="VerifyPasswordRequest",
 *    type="object",
 *    title="Verify Password Request",
 *    required={"currentPassword", "id_empleado"},
 *    @OA\Property(property="currentPassword", type="string", format="password", example="mi_contraseña", description="Contraseña actual a verificar"),
 *    @OA\Property(property="id_empleado", type="integer", example=1, description="ID del empleado")
 * )
 *
 * @OA\Schema(
 *    schema="VerifyPasswordResponse",
 *    type="object",
 *    title="Verify Password Response",
 *    @OA\Property(property="valid", type="boolean", example=true),
 *    @OA\Property(property="message", type="string", example="Contraseña verificada correctamente")
 * )
 *
 * @OA\Schema(
 *    schema="UploadSignatureRequest",
 *    type="object",
 *    title="Upload Signature Request",
 *    required={"timestamp"},
 *    @OA\Property(property="timestamp", type="integer", example=1710854400, description="Timestamp Unix actual en segundos"),
 *    @OA\Property(property="resource_type", type="string", example="image", description="Tipo de recurso Cloudinary"),
 *    @OA\Property(property="allowed_formats", type="string", example="jpeg,jpg,png", description="Formatos permitidos")
 * )
 *
 * @OA\Schema(
 *    schema="UploadSignatureResponse",
 *    type="object",
 *    title="Upload Signature Response",
 *    @OA\Property(property="public_id", type="string", example="empleados/perfiles/1/profile"),
 *    @OA\Property(property="folder", type="string", example="empleados/perfiles/1"),
 *    @OA\Property(property="signature", type="string", example="abcdef123456...", description="Firma de Cloudinary"),
 *    @OA\Property(property="timestamp", type="integer", example=1710854400),
 *    @OA\Property(property="api_key", type="string", example="1234567890abcdef"),
 *    @OA\Property(property="cloud_name", type="string", example="digimedia")
 * )
 *
 * @OA\Schema(
 *    schema="UpdateProfileImageRequest",
 *    type="object",
 *    title="Update Profile Image Request",
 *    required={"public_id", "secure_url"},
 *    @OA\Property(property="public_id", type="string", example="empleados/perfiles/1/profile", description="Public ID de la imagen en Cloudinary"),
 *    @OA\Property(property="secure_url", type="string", format="url", example="https://res.cloudinary.com/digimedia/image/upload/v123/empleados/perfiles/1/profile.jpg", description="URL segura de la imagen desde Cloudinary")
 * )
 *
 * @OA\Schema(
 *    schema="UpdateProfileImageResponse",
 *    type="object",
 *    title="Update Profile Image Response",
 *    @OA\Property(property="status", type="integer", example=200),
 *    @OA\Property(property="message", type="string", example="Imagen de perfil actualizada correctamente"),
 *    @OA\Property(property="image_url", type="string", format="url", example="https://res.cloudinary.com/digimedia/image/upload/v123/empleados/perfiles/1/profile.jpg")
 * )
 *
 * @OA\Schema(
 *    schema="DeleteProfileImageResponse",
 *    type="object",
 *    title="Delete Profile Image Response",
 *    @OA\Property(property="status", type="integer", example=200),
 *    @OA\Property(property="message", type="string", example="Imagen de perfil eliminada correctamente")
 * )
 *
 * @OA\PathItem(path="/logout")
 * @OA\Post(
 *    path="/logout",
 *    operationId="logout",
 *    tags={"Authentication"},
 *    summary="Cerrar sesión",
 *    description="Cierra la sesión del usuario autenticado eliminando su token de acceso actual.",
 *    security={{"sanctum": {}}},
 *    @OA\Response(
 *       response=200,
 *       description="Sesión cerrada exitosamente",
 *       @OA\JsonContent(ref="#/components/schemas/LogoutResponse")
 *    ),
 *    @OA\Response(
 *       response=401,
 *       description="No autenticado",
 *       @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *    )
 * )
 *
 * @OA\PathItem(path="/me")
 * @OA\Get(
 *    path="/me",
 *    operationId="getAuthenticatedUser",
 *    tags={"Authentication"},
 *    summary="Obtener datos del usuario autenticado",
 *    description="Retorna la información del usuario autenticado, su empleado asociado, rol y permisos.",
 *    security={{"sanctum": {}}},
 *    @OA\Response(
 *       response=200,
 *       description="Datos del usuario autenticado",
 *       @OA\JsonContent(ref="#/components/schemas/MeResponse")
 *    ),
 *    @OA\Response(
 *       response=401,
 *       description="No autenticado",
 *       @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *    )
 * )
 *
 * @OA\PathItem(path="/empleados/verify-password")
 * @OA\Post(
 *    path="/empleados/verify-password",
 *    operationId="verifyPassword",
 *    tags={"Authentication"},
 *    summary="Verificar contraseña actual",
 *    description="Verifica que la contraseña proporcionada coincida con la contraseña actual del empleado.",
 *    security={{"sanctum": {}}},
 *    @OA\RequestBody(
 *       required=true,
 *       description="Datos para verificar contraseña",
 *       @OA\JsonContent(ref="#/components/schemas/VerifyPasswordRequest")
 *    ),
 *    @OA\Response(
 *       response=200,
 *       description="Verificación exitosa",
 *       @OA\JsonContent(ref="#/components/schemas/VerifyPasswordResponse")
 *    ),
 *    @OA\Response(
 *       response=400,
 *       description="Contraseña incorrecta",
 *       @OA\JsonContent(
 *          @OA\Property(property="valid", type="boolean", example=false),
 *          @OA\Property(property="message", type="string", example="La contraseña actual es incorrecta")
 *       )
 *    ),
 *    @OA\Response(
 *       response=404,
 *       description="Empleado no encontrado",
 *       @OA\JsonContent(
 *          @OA\Property(property="valid", type="boolean", example=false),
 *          @OA\Property(property="message", type="string", example="No se encontró el usuario asociado al empleado")
 *       )
 *    ),
 *    @OA\Response(
 *       response=422,
 *       description="Error de validación",
 *       @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *    )
 * )
 *
 * @OA\PathItem(path="/empleados/{id}/upload-signature")
 * @OA\Post(
 *    path="/empleados/{id}/upload-signature",
 *    operationId="generateUploadSignature",
 *    tags={"Authentication"},
 *    summary="Generar firma de carga para Cloudinary",
 *    description="Genera una firma de autenticación válida para permitir cargas de imágenes a Cloudinary. La firma es válida por 2 minutos.",
 *    security={{"sanctum": {}}},
 *    @OA\Parameter(
 *       name="id",
 *       in="path",
 *       required=true,
 *       description="ID del empleado",
 *       @OA\Schema(type="integer")
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       description="Datos para generar firma",
 *       @OA\JsonContent(ref="#/components/schemas/UploadSignatureRequest")
 *    ),
 *    @OA\Response(
 *       response=200,
 *       description="Firma generada exitosamente",
 *       @OA\JsonContent(ref="#/components/schemas/UploadSignatureResponse")
 *    ),
 *    @OA\Response(
 *       response=403,
 *       description="Sin permiso para subir imágenes",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="integer", example=403),
 *          @OA\Property(property="message", type="string", example="No tienes permiso para subir imágenes en este perfil")
 *       )
 *    ),
 *    @OA\Response(
 *       response=404,
 *       description="Empleado no encontrado",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="integer", example=404),
 *          @OA\Property(property="message", type="string", example="Empleado no encontrado")
 *       )
 *    ),
 *    @OA\Response(
 *       response=422,
 *       description="Timestamp expirado o inválido",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="integer", example=422),
 *          @OA\Property(property="message", type="string", example="Firma expirada, por favor reintente")
 *       )
 *    )
 * )
 *
 * @OA\PathItem(path="/empleados/{id}/image")
 * @OA\Post(
 *    path="/empleados/{id}/image",
 *    operationId="updateProfileImage",
 *    tags={"Authentication"},
 *    summary="Actualizar imagen de perfil",
 *    description="Actualiza la imagen de perfil del empleado con una URL validada desde Cloudinary.",
 *    security={{"sanctum": {}}},
 *    @OA\Parameter(
 *       name="id",
 *       in="path",
 *       required=true,
 *       description="ID del empleado",
 *       @OA\Schema(type="integer")
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       description="Datos de la imagen",
 *       @OA\JsonContent(ref="#/components/schemas/UpdateProfileImageRequest")
 *    ),
 *    @OA\Response(
 *       response=200,
 *       description="Imagen actualizada exitosamente",
 *       @OA\JsonContent(ref="#/components/schemas/UpdateProfileImageResponse")
 *    ),
 *    @OA\Response(
 *       response=403,
 *       description="Sin permiso para modificar este perfil",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="integer", example=403),
 *          @OA\Property(property="message", type="string", example="No tienes permiso para modificar este perfil")
 *       )
 *    ),
 *    @OA\Response(
 *       response=404,
 *       description="Empleado no encontrado",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="integer", example=404),
 *          @OA\Property(property="message", type="string", example="Empleado no encontrado")
 *       )
 *    ),
 *    @OA\Response(
 *       response=422,
 *       description="Error de validación",
 *       @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *    )
 * )
 * @OA\Delete(
 *    path="/empleados/{id}/image",
 *    operationId="deleteProfileImage",
 *    tags={"Authentication"},
 *    summary="Eliminar imagen de perfil",
 *    description="Elimina la imagen de perfil del empleado de Cloudinary y de la base de datos.",
 *    security={{"sanctum": {}}},
 *    @OA\Parameter(
 *       name="id",
 *       in="path",
 *       required=true,
 *       description="ID del empleado",
 *       @OA\Schema(type="integer")
 *    ),
 *    @OA\Response(
 *       response=200,
 *       description="Imagen eliminada exitosamente",
 *       @OA\JsonContent(ref="#/components/schemas/DeleteProfileImageResponse")
 *    ),
 *    @OA\Response(
 *       response=403,
 *       description="Sin permiso para modificar este perfil",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="integer", example=403),
 *          @OA\Property(property="message", type="string", example="No tienes permiso para modificar este perfil")
 *       )
 *    ),
 *    @OA\Response(
 *       response=404,
 *       description="Empleado no encontrado",
 *       @OA\JsonContent(
 *          @OA\Property(property="status", type="integer", example=404),
 *          @OA\Property(property="message", type="string", example="Empleado no encontrado")
 *       )
 *    )
 * )
 */

namespace App\Http\Controllers\Api;

// Este archivo solo contiene anotaciones de documentación Swagger
// No tiene código de ejecución real
