<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddAuthenticationDocs extends Command
{
    protected $signature = 'docs:add-authentication';
    protected $description = 'Agregar documentación Swagger para endpoints de autenticación';

    public function handle()
    {
        $jsonPath = storage_path('api-docs/api-docs.json');
        
        if (!File::exists($jsonPath)) {
            $this->error('Archivo api-docs.json no encontrado');
            return 1;
        }

        $json = json_decode(File::get($jsonPath), true);

        // Agregar schemas
        $schemas = [
            'LogoutResponse' => [
                'type' => 'object',
                'title' => 'Logout Response',
                'properties' => [
                    'status' => ['type' => 'string', 'example' => 'success'],
                    'message' => ['type' => 'string', 'example' => 'Sesión cerrada exitosamente'],
                ]
            ],
            'MeResponse' => [
                'type' => 'object',
                'title' => 'Me Response',
                'properties' => [
                    'user' => ['$ref' => '#/components/schemas/UserResponse'],
                    'empleado' => ['$ref' => '#/components/schemas/EmpleadoResponse'],
                    'rol' => ['type' => 'string', 'example' => 'administrador'],
                    'abilities' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'permisos' => ['type' => 'array', 'items' => ['type' => 'string', 'example' => 'ver-blogs']],
                ]
            ],
            'VerifyPasswordRequest' => [
                'type' => 'object',
                'title' => 'Verify Password Request',
                'required' => ['currentPassword', 'id_empleado'],
                'properties' => [
                    'currentPassword' => ['type' => 'string', 'format' => 'password', 'example' => 'mi_contraseña'],
                    'id_empleado' => ['type' => 'integer', 'example' => 1],
                ]
            ],
            'VerifyPasswordResponse' => [
                'type' => 'object',
                'title' => 'Verify Password Response',
                'properties' => [
                    'valid' => ['type' => 'boolean', 'example' => true],
                    'message' => ['type' => 'string', 'example' => 'Contraseña verificada correctamente'],
                ]
            ],
            'UploadSignatureRequest' => [
                'type' => 'object',
                'title' => 'Upload Signature Request',
                'required' => ['timestamp'],
                'properties' => [
                    'timestamp' => ['type' => 'integer', 'example' => 1710854400],
                    'resource_type' => ['type' => 'string', 'example' => 'image'],
                    'allowed_formats' => ['type' => 'string', 'example' => 'jpeg,jpg,png'],
                ]
            ],
            'UploadSignatureResponse' => [
                'type' => 'object',
                'title' => 'Upload Signature Response',
                'properties' => [
                    'public_id' => ['type' => 'string', 'example' => 'empleados/perfiles/1/profile'],
                    'folder' => ['type' => 'string', 'example' => 'empleados/perfiles/1'],
                    'signature' => ['type' => 'string', 'example' => 'abcdef123456...'],
                    'timestamp' => ['type' => 'integer', 'example' => 1710854400],
                    'api_key' => ['type' => 'string', 'example' => '1234567890abcdef'],
                    'cloud_name' => ['type' => 'string', 'example' => 'digimedia'],
                ]
            ],
            'UpdateProfileImageRequest' => [
                'type' => 'object',
                'title' => 'Update Profile Image Request',
                'required' => ['public_id', 'secure_url'],
                'properties' => [
                    'public_id' => ['type' => 'string', 'example' => 'empleados/perfiles/1/profile'],
                    'secure_url' => ['type' => 'string', 'format' => 'url', 'example' => 'https://res.cloudinary.com/digimedia/image/upload/v123/empleados/perfiles/1/profile.jpg'],
                ]
            ],
            'UpdateProfileImageResponse' => [
                'type' => 'object',
                'title' => 'Update Profile Image Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'message' => ['type' => 'string', 'example' => 'Imagen de perfil actualizada correctamente'],
                    'image_url' => ['type' => 'string', 'format' => 'url', 'example' => 'https://res.cloudinary.com/digimedia/image/upload/v123/empleados/perfiles/1/profile.jpg'],
                ]
            ],
            'DeleteProfileImageResponse' => [
                'type' => 'object',
                'title' => 'Delete Profile Image Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'message' => ['type' => 'string', 'example' => 'Imagen de perfil eliminada correctamente'],
                ]
            ],
        ];

        foreach ($schemas as $name => $schema) {
            if (!isset($json['components']['schemas'][$name])) {
                $json['components']['schemas'][$name] = $schema;
                $this->line("✓ Schema agregado: $name");
            }
        }

        // Agregar endpoints
        $endpoints = [
            '/logout' => [
                'post' => [
                    'operationId' => 'logout',
                    'tags' => ['Authentication'],
                    'summary' => 'Cerrar sesión',
                    'description' => 'Cierra la sesión del usuario autenticado eliminando su token de acceso actual.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Sesión cerrada exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/LogoutResponse']]],
                        ],
                        '401' => [
                            'description' => 'No autenticado',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ErrorResponse']]],
                        ],
                    ],
                ]
            ],
            '/me' => [
                'get' => [
                    'operationId' => 'getAuthenticatedUser',
                    'tags' => ['Authentication'],
                    'summary' => 'Obtener datos del usuario autenticado',
                    'description' => 'Retorna la información del usuario autenticado, su empleado asociado, rol y permisos.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Datos del usuario autenticado',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MeResponse']]],
                        ],
                        '401' => [
                            'description' => 'No autenticado',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ErrorResponse']]],
                        ],
                    ],
                ]
            ],
            '/empleados/verify-password' => [
                'post' => [
                    'operationId' => 'verifyPassword',
                    'tags' => ['Authentication'],
                    'summary' => 'Verificar contraseña actual',
                    'description' => 'Verifica que la contraseña proporcionada coincida con la contraseña actual del empleado.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/VerifyPasswordRequest']]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Verificación exitosa',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/VerifyPasswordResponse']]],
                        ],
                        '400' => [
                            'description' => 'Contraseña incorrecta',
                        ],
                        '404' => [
                            'description' => 'Empleado no encontrado',
                        ],
                        '422' => [
                            'description' => 'Error de validación',
                        ],
                    ],
                ]
            ],
            '/empleados/{id}/upload-signature' => [
                'post' => [
                    'operationId' => 'generateUploadSignature',
                    'tags' => ['Authentication'],
                    'summary' => 'Generar firma de carga para Cloudinary',
                    'description' => 'Genera una firma de autenticación válida para permitir cargas de imágenes a Cloudinary.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer'],
                        ]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/UploadSignatureRequest']]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Firma generada exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/UploadSignatureResponse']]],
                        ],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Empleado no encontrado'],
                        '422' => ['description' => 'Timestamp inválido'],
                    ],
                ]
            ],
            '/empleados/{id}/image' => [
                'post' => [
                    'operationId' => 'updateProfileImage',
                    'tags' => ['Authentication'],
                    'summary' => 'Actualizar imagen de perfil',
                    'description' => 'Actualiza la imagen de perfil del empleado con una URL validada desde Cloudinary.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer'],
                        ]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/UpdateProfileImageRequest']]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Imagen actualizada exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/UpdateProfileImageResponse']]],
                        ],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Empleado no encontrado'],
                        '422' => ['description' => 'Error de validación'],
                    ],
                ]
            ],
        ];

        // Agregar el DELETE en el mismo endpoint /empleados/{id}/image
        $endpoints['/empleados/{id}/image']['delete'] = [
            'operationId' => 'deleteProfileImage',
            'tags' => ['Authentication'],
            'summary' => 'Eliminar imagen de perfil',
            'description' => 'Elimina la imagen de perfil del empleado de Cloudinary y de la base de datos.',
            'security' => [['sanctum' => []]],
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => ['type' => 'integer'],
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Imagen eliminada exitosamente',
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/DeleteProfileImageResponse']]],
                ],
                '403' => ['description' => 'Sin permiso'],
                '404' => ['description' => 'Empleado no encontrado'],
            ],
        ];

        foreach ($endpoints as $path => $methods) {
            if (!isset($json['paths'][$path])) {
                $json['paths'][$path] = [];
            }
            foreach ($methods as $method => $operation) {
                if (!isset($json['paths'][$path][$method])) {
                    $json['paths'][$path][$method] = $operation;
                    $this->line("✓ Endpoint agregado: $method $path");
                }
            }
        }

        // Guardar
        File::put($jsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info('✅ Documentación Swagger actualizada exitosamente');

        return 0;
    }
}
