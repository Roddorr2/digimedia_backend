<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddEmployeesDocs extends Command
{
    protected $signature = 'docs:add-employees';
    protected $description = 'Agregar documentación Swagger para endpoints de empleados';

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
            'EmployeeCreateRequest' => [
                'type' => 'object',
                'title' => 'Employee Create Request',
                'required' => ['nombre', 'apellido', 'email', 'dni', 'id_rol'],
                'properties' => [
                    'nombre' => ['type' => 'string', 'example' => 'Juan', 'maxLength' => 255],
                    'apellido' => ['type' => 'string', 'example' => 'Pérez', 'maxLength' => 255],
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'juan@example.com'],
                    'dni' => ['type' => 'string', 'example' => '12345678', 'maxLength' => 20],
                    'telefono' => ['type' => 'string', 'nullable' => true, 'example' => '987654321'],
                    'id_rol' => ['type' => 'integer', 'example' => 2],
                ]
            ],
            'EmployeeUpdateRequest' => [
                'type' => 'object',
                'title' => 'Employee Update Request',
                'properties' => [
                    'nombre' => ['type' => 'string', 'example' => 'Juan'],
                    'apellido' => ['type' => 'string', 'example' => 'Pérez'],
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'juan@example.com'],
                    'dni' => ['type' => 'string', 'example' => '12345678'],
                    'telefono' => ['type' => 'string', 'nullable' => true],
                    'id_rol' => ['type' => 'integer', 'example' => 2],
                ]
            ],
            'UpdatePasswordRequest' => [
                'type' => 'object',
                'title' => 'Update Password Request',
                'required' => ['password', 'password_confirmation'],
                'properties' => [
                    'password' => ['type' => 'string', 'format' => 'password', 'example' => 'nueva_contraseña', 'minLength' => 6],
                    'password_confirmation' => ['type' => 'string', 'format' => 'password', 'example' => 'nueva_contraseña'],
                ]
            ],
            'EmployeeDetailResponse' => [
                'type' => 'object',
                'title' => 'Employee Detail Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'id_empleado' => ['type' => 'integer'],
                            'nombre' => ['type' => 'string'],
                            'apellido' => ['type' => 'string'],
                            'email' => ['type' => 'string'],
                            'dni' => ['type' => 'string'],
                            'telefono' => ['type' => 'string', 'nullable' => true],
                            'id_user' => ['type' => 'integer'],
                            'id_rol' => ['type' => 'integer'],
                            'rol' => ['type' => 'object', 'properties' => ['id_rol' => ['type' => 'integer'], 'nombre' => ['type' => 'string']]],
                        ]
                    ]
                ]
            ],
            'EmployeeListResponse' => [
                'type' => 'object',
                'title' => 'Employee List Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'total' => ['type' => 'integer'],
                            'data' => ['type' => 'array', 'items' => ['type' => 'object']],
                            'current_page' => ['type' => 'integer'],
                        ]
                    ]
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
            '/empleados' => [
                'get' => [
                    'operationId' => 'listEmployees',
                    'tags' => ['Empleados'],
                    'summary' => 'Listar empleados',
                    'description' => 'Obtiene la lista de empleados con paginación, búsqueda y filtros opcionales.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'search',
                            'in' => 'query',
                            'schema' => ['type' => 'string'],
                            'description' => 'Buscar por nombre, apellido o email'
                        ],
                        [
                            'name' => 'rol',
                            'in' => 'query',
                            'schema' => ['type' => 'string'],
                            'description' => 'Filtrar por rol (ej: all, administrador)'
                        ],
                        [
                            'name' => 'limit',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'default' => 5],
                            'description' => 'Cantidad de registros por página'
                        ],
                        [
                            'name' => 'sortBy',
                            'in' => 'query',
                            'schema' => ['type' => 'string', 'default' => 'id_empleado'],
                            'description' => 'Campo para ordenar'
                        ],
                        [
                            'name' => 'sortOrder',
                            'in' => 'query',
                            'schema' => ['type' => 'string', 'enum' => ['asc', 'desc'], 'default' => 'asc'],
                            'description' => 'Orden de clasificación'
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de empleados',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/EmployeeListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'post' => [
                    'operationId' => 'createEmployee',
                    'tags' => ['Empleados'],
                    'summary' => 'Crear nuevo empleado',
                    'description' => 'Crea un nuevo empleado en el sistema. Se genera una contraseña temporal.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/EmployeeCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Empleado creado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Error de validación - Email o DNI duplicado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/empleados/{id}' => [
                'get' => [
                    'operationId' => 'getEmployee',
                    'tags' => ['Empleados'],
                    'summary' => 'Obtener detalles de empleado',
                    'description' => 'Retorna los detalles completos de un empleado específico. Los admins ven DNI completo; otros ven parcialmente enmascarado.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Detalles del empleado',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/EmployeeDetailResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Empleado no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'put' => [
                    'operationId' => 'updateEmployee',
                    'tags' => ['Empleados'],
                    'summary' => 'Actualizar empleado',
                    'description' => 'Actualiza la información de un empleado. Solo administradores pueden actualizar ciertos campos.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/EmployeeUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Empleado actualizado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para modificar este empleado'],
                        '404' => ['description' => 'Empleado no encontrado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteEmployee',
                    'tags' => ['Empleados'],
                    'summary' => 'Eliminar empleado',
                    'description' => 'Elimina un empleado del sistema.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Empleado eliminado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Empleado no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/empleados/pass/{id}' => [
                'put' => [
                    'operationId' => 'updateEmployeePassword',
                    'tags' => ['Empleados'],
                    'summary' => 'Actualizar contraseña de empleado',
                    'description' => 'Actualiza la contraseña de un empleado. La nueva contraseña debe estar confirmada.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/UpdatePasswordRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Contraseña actualizada exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Empleado no encontrado'],
                        '422' => ['description' => 'Error de validación - Las contraseñas no coinciden'],
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
        $this->info('✅ Documentación de Empleados actualizada exitosamente');

        return 0;
    }
}
