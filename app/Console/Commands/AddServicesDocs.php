<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddServicesDocs extends Command
{
    protected $signature = 'docs:add-services';
    protected $description = 'Agregar documentación Swagger para endpoints de servicios';

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
            'ServiceCreateRequest' => [
                'type' => 'object',
                'title' => 'Service Create Request',
                'required' => ['nombre', 'descripcion'],
                'properties' => [
                    'nombre' => ['type' => 'string', 'example' => 'Consultoría', 'maxLength' => 100],
                    'descripcion' => ['type' => 'string', 'example' => 'Servicios de consultoría empresarial', 'maxLength' => 200],
                ]
            ],
            'ServiceUpdateRequest' => [
                'type' => 'object',
                'title' => 'Service Update Request',
                'required' => ['nombre', 'descripcion'],
                'properties' => [
                    'nombre' => ['type' => 'string', 'example' => 'Consultoría', 'maxLength' => 100],
                    'descripcion' => ['type' => 'string', 'example' => 'Servicios de consultoría empresarial', 'maxLength' => 200],
                ]
            ],
            'ServiceResponse' => [
                'type' => 'object',
                'title' => 'Service Response',
                'properties' => [
                    'id_servicio' => ['type' => 'integer', 'example' => 1],
                    'nombre' => ['type' => 'string', 'example' => 'Consultoría'],
                    'descripcion' => ['type' => 'string', 'example' => 'Servicios de consultoría empresarial'],
                ]
            ],
            'ServiceListResponse' => [
                'type' => 'object',
                'title' => 'Service List Response',
                'properties' => [
                    'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/ServiceResponse']],
                    'meta' => [
                        'type' => 'object',
                        'properties' => [
                            'current_page' => ['type' => 'integer'],
                            'from' => ['type' => 'integer'],
                            'last_page' => ['type' => 'integer'],
                            'per_page' => ['type' => 'integer'],
                            'to' => ['type' => 'integer'],
                            'total' => ['type' => 'integer'],
                        ]
                    ]
                ]
            ],
            'ServiceUpdateResponse' => [
                'type' => 'object',
                'title' => 'Service Update Response',
                'properties' => [
                    'status' => ['type' => 'boolean', 'example' => true],
                    'message' => ['type' => 'string', 'example' => 'Servicio actualizado exitosamente'],
                    'data' => ['$ref' => '#/components/schemas/ServiceResponse'],
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
            '/servicios' => [
                'get' => [
                    'operationId' => 'listServices',
                    'tags' => ['Servicios'],
                    'summary' => 'Listar servicios',
                    'description' => 'Obtiene la lista de servicios disponibles con paginación.',
                    'parameters' => [
                        [
                            'name' => 'page',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'default' => 1],
                            'description' => 'Número de página'
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de servicios',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ServiceListResponse']]],
                        ],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'post' => [
                    'operationId' => 'createService',
                    'tags' => ['Servicios'],
                    'summary' => 'Crear nuevo servicio',
                    'description' => 'Crea un nuevo servicio en el sistema. Solo administradores con permiso crear-servicios.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ServiceCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Servicio creado exitosamente'],
                        '400' => ['description' => 'Error de validación'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para crear servicios'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/servicios/{id}' => [
                'put' => [
                    'operationId' => 'updateService',
                    'tags' => ['Servicios'],
                    'summary' => 'Actualizar servicio',
                    'description' => 'Actualiza la información de un servicio. Solo administradores con permiso editar-servicios.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del servicio']
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ServiceUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Servicio actualizado exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ServiceUpdateResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para editar servicios'],
                        '404' => ['description' => 'Servicio no encontrado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteService',
                    'tags' => ['Servicios'],
                    'summary' => 'Eliminar servicio',
                    'description' => 'Elimina un servicio del sistema. Solo administradores con permiso eliminar-servicios.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del servicio']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Servicio eliminado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para eliminar servicios'],
                        '404' => ['description' => 'Servicio no encontrado'],
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
        $this->info('✅ Documentación de Servicios actualizada exitosamente');

        return 0;
    }
}
