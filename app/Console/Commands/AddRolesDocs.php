<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddRolesDocs extends Command
{
    protected $signature = 'docs:add-roles';
    protected $description = 'Agregar documentación Swagger para endpoints de roles y permisos';

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
            'RoleCreateRequest' => [
                'type' => 'object',
                'title' => 'Role Create Request',
                'required' => ['nombre'],
                'properties' => [
                    'nombre' => ['type' => 'string', 'example' => 'editor', 'description' => 'Nombre único del rol'],
                    'permisos' => ['type' => 'array', 'items' => ['type' => 'integer'], 'example' => [1, 2, 3], 'description' => 'IDs de permisos a asignar (opcional)'],
                ]
            ],
            'RoleUpdateRequest' => [
                'type' => 'object',
                'title' => 'Role Update Request',
                'required' => ['nombre'],
                'properties' => [
                    'nombre' => ['type' => 'string', 'example' => 'editor_premium', 'description' => 'Nuevo nombre del rol'],
                    'permisos' => ['type' => 'array', 'items' => ['type' => 'integer'], 'example' => [1, 2, 3, 4], 'description' => 'IDs de permisos a sincronizar (opcional)'],
                ]
            ],
            'PermissionData' => [
                'type' => 'object',
                'title' => 'Permission Data',
                'properties' => [
                    'id_permiso' => ['type' => 'integer', 'example' => 1],
                    'nombre' => ['type' => 'string', 'example' => 'Ver blogs'],
                    'slug' => ['type' => 'string', 'example' => 'ver-blogs'],
                    'descripcion' => ['type' => 'string', 'nullable' => true, 'example' => 'Permite ver todos los blogs'],
                ]
            ],
            'RoleDetailResponse' => [
                'type' => 'object',
                'title' => 'Role Detail Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'id_rol' => ['type' => 'integer', 'example' => 1],
                            'nombre' => ['type' => 'string', 'example' => 'administrador'],
                            'permisos' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/PermissionData']],
                        ]
                    ]
                ]
            ],
            'RoleListResponse' => [
                'type' => 'object',
                'title' => 'Role List Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'data' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'id_rol' => ['type' => 'integer'],
                                'nombre' => ['type' => 'string'],
                            ]
                        ]
                    ]
                ]
            ],
            'RoleCreateResponse' => [
                'type' => 'object',
                'title' => 'Role Create Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 201],
                    'message' => ['type' => 'string', 'example' => 'Rol creado correctamente'],
                    'data' => ['type' => 'object']
                ]
            ],
            'PermissionListResponse' => [
                'type' => 'object',
                'title' => 'Permission List Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'data' => [
                        'type' => 'array',
                        'items' => ['$ref' => '#/components/schemas/PermissionData']
                    ]
                ]
            ],
            'SyncPermissionsRequest' => [
                'type' => 'object',
                'title' => 'Sync Permissions Request',
                'required' => ['permisos'],
                'properties' => [
                    'permisos' => ['type' => 'array', 'items' => ['type' => 'integer'], 'example' => [1, 2, 3, 4, 5], 'description' => 'IDs de permisos a sincronizar'],
                ]
            ],
            'SyncPermissionsResponse' => [
                'type' => 'object',
                'title' => 'Sync Permissions Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'message' => ['type' => 'string', 'example' => 'Permisos actualizados correctamente'],
                    'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/PermissionData']],
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
            '/roles' => [
                'get' => [
                    'operationId' => 'listRoles',
                    'tags' => ['Roles & Permisos'],
                    'summary' => 'Listar todos los roles',
                    'description' => 'Retorna una lista de todos los roles del sistema.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de roles obtenida exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RoleListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'post' => [
                    'operationId' => 'createRole',
                    'tags' => ['Roles & Permisos'],
                    'summary' => 'Crear nuevo rol',
                    'description' => 'Crea un nuevo rol en el sistema y opcionalmente asigna permisos.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RoleCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Rol creado exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RoleCreateResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Error de validación - El nombre del rol debe ser único'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/roles/{id}' => [
                'get' => [
                    'operationId' => 'getRole',
                    'tags' => ['Roles & Permisos'],
                    'summary' => 'Obtener detalles de un rol',
                    'description' => 'Retorna los detalles de un rol específico incluyendo sus permisos asignados.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'description' => 'ID del rol',
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Detalles del rol',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RoleDetailResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Rol no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'put' => [
                    'operationId' => 'updateRole',
                    'tags' => ['Roles & Permisos'],
                    'summary' => 'Actualizar rol',
                    'description' => 'Actualiza el nombre y/o permisos de un rol existente.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'description' => 'ID del rol',
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RoleUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Rol actualizado exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RoleDetailResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Rol no encontrado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteRole',
                    'tags' => ['Roles & Permisos'],
                    'summary' => 'Eliminar rol',
                    'description' => 'Elimina un rol del sistema. No se puede eliminar si tiene empleados asociados.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'description' => 'ID del rol',
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Rol eliminado exitosamente',
                            'content' => ['application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'integer', 'example' => 200],
                                        'message' => ['type' => 'string', 'example' => 'Rol eliminado correctamente'],
                                    ]
                                ]
                            ]],
                        ],
                        '400' => [
                            'description' => 'No se puede eliminar - El rol tiene empleados asociados',
                            'content' => ['application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'integer', 'example' => 400],
                                        'error' => ['type' => 'string'],
                                    ]
                                ]
                            ]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Rol no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/roles/{id}/permisos' => [
                'get' => [
                    'operationId' => 'getRolePermissions',
                    'tags' => ['Roles & Permisos'],
                    'summary' => 'Obtener permisos de un rol',
                    'description' => 'Retorna la lista de permisos asignados a un rol específico.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'description' => 'ID del rol',
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Permisos del rol',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PermissionListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Rol no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'post' => [
                    'operationId' => 'syncRolePermissions',
                    'tags' => ['Roles & Permisos'],
                    'summary' => 'Sincronizar permisos del rol',
                    'description' => 'Actualiza los permisos del rol. Los permisos especificados reemplazan los anteriores.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'description' => 'ID del rol',
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/SyncPermissionsRequest']]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Permisos sincronizados exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/SyncPermissionsResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Rol no encontrado'],
                        '422' => ['description' => 'Error de validación - IDs de permisos inválidos'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
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
        $this->info('✅ Documentación de Roles y Permisos actualizada exitosamente');

        return 0;
    }
}
