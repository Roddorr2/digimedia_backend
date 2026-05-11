<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddReclamacionesDocs extends Command
{
    protected $signature = 'docs:add-reclamaciones';
    protected $description = 'Agregar documentación Swagger para endpoints de reclamaciones';

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
            'ReclamacionCreateRequest' => [
                'type' => 'object',
                'title' => 'Reclamación Create Request',
                'required' => ['nombre', 'apellido', 'documento', 'numeroDocumento', 'email', 'celular', 'direccion', 'distrito', 'ciudad', 'tipoReclamo', 'id_servicio', 'reclamoPerson', 'checkReclamoForm', 'aceptaPoliticaPrivacidad', 'fechaIncidente'],
                'properties' => [
                    'nombre' => ['type' => 'string', 'example' => 'Juan', 'maxLength' => 100],
                    'apellido' => ['type' => 'string', 'example' => 'Pérez', 'maxLength' => 100],
                    'documento' => ['type' => 'string', 'example' => 'DNI', 'maxLength' => 100],
                    'numeroDocumento' => ['type' => 'string', 'example' => '12345678', 'maxLength' => 100],
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'juan@example.com', 'maxLength' => 100],
                    'celular' => ['type' => 'string', 'example' => '+51987654321', 'maxLength' => 20],
                    'direccion' => ['type' => 'string', 'example' => 'Calle Principal 123', 'maxLength' => 250],
                    'distrito' => ['type' => 'string', 'example' => 'Miraflores', 'maxLength' => 250],
                    'ciudad' => ['type' => 'string', 'example' => 'Lima', 'maxLength' => 250],
                    'tipoReclamo' => ['type' => 'string', 'example' => 'Producto Defectuoso', 'maxLength' => 20],
                    'id_servicio' => ['type' => 'integer', 'example' => 1, 'minimum' => 1, 'maximum' => 4],
                    'reclamoPerson' => ['type' => 'string', 'example' => 'Descripción detallada de la reclamación...', 'maxLength' => 1050],
                    'checkReclamoForm' => ['type' => 'boolean', 'example' => true],
                    'aceptaPoliticaPrivacidad' => ['type' => 'boolean', 'example' => true],
                    'fechaIncidente' => ['type' => 'string', 'format' => 'date', 'example' => '2026-04-20'],
                ]
            ],
            'ReclamacionUpdateRequest' => [
                'type' => 'object',
                'title' => 'Reclamación Update Request',
                'required' => ['estado'],
                'properties' => [
                    'estado' => ['type' => 'string', 'enum' => ['PENDIENTE', 'ATENDIDO'], 'example' => 'ATENDIDO'],
                ]
            ],
            'ReclamacionResponse' => [
                'type' => 'object',
                'title' => 'Reclamación Response',
                'properties' => [
                    'id_reclamacion' => ['type' => 'integer', 'example' => 1],
                    'nombre' => ['type' => 'string', 'example' => 'Juan'],
                    'apellido' => ['type' => 'string', 'example' => 'Pérez'],
                    'documento' => ['type' => 'string', 'example' => 'DNI'],
                    'numeroDocumento' => ['type' => 'string', 'example' => '12345678'],
                    'email' => ['type' => 'string', 'example' => 'juan@example.com'],
                    'celular' => ['type' => 'string', 'example' => '+51987654321'],
                    'direccion' => ['type' => 'string', 'example' => 'Calle Principal 123'],
                    'distrito' => ['type' => 'string', 'example' => 'Miraflores'],
                    'ciudad' => ['type' => 'string', 'example' => 'Lima'],
                    'tipoReclamo' => ['type' => 'string', 'example' => 'Producto Defectuoso'],
                    'id_servicio' => ['type' => 'integer', 'example' => 1],
                    'reclamoPerson' => ['type' => 'string', 'example' => 'Descripción de la reclamación'],
                    'checkReclamoForm' => ['type' => 'boolean', 'example' => true],
                    'aceptaPoliticaPrivacidad' => ['type' => 'boolean', 'example' => true],
                    'fechaReclamo' => ['type' => 'string', 'format' => 'date', 'example' => '2026-04-21'],
                    'fechaIncidente' => ['type' => 'string', 'format' => 'date', 'example' => '2026-04-20'],
                    'estadoReclamo' => ['type' => 'string', 'enum' => ['PENDIENTE', 'ATENDIDO'], 'example' => 'PENDIENTE'],
                ]
            ],
            'ReclamacionListResponse' => [
                'type' => 'object',
                'title' => 'Reclamación List Response',
                'properties' => [
                    'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/ReclamacionResponse']],
                    'current_page' => ['type' => 'integer', 'example' => 1],
                    'from' => ['type' => 'integer', 'example' => 1],
                    'last_page' => ['type' => 'integer', 'example' => 5],
                    'per_page' => ['type' => 'integer', 'example' => 4],
                    'to' => ['type' => 'integer', 'example' => 4],
                    'total' => ['type' => 'integer', 'example' => 18],
                ]
            ],
            'ReclamacionDetailResponse' => [
                'type' => 'object',
                'title' => 'Reclamación Detail Response',
                'properties' => [
                    'status' => ['type' => 'string', 'example' => 'success'],
                    'data' => ['$ref' => '#/components/schemas/ReclamacionResponse'],
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
            '/reclamaciones' => [
                'post' => [
                    'operationId' => 'createReclamacion',
                    'tags' => ['Reclamaciones'],
                    'summary' => 'Crear reclamación',
                    'description' => 'Registra una nueva reclamación en el libro de reclamaciones. Este endpoint es público y no requiere autenticación.',
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ReclamacionCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Reclamación guardada exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ReclamacionDetailResponse']]],
                        ],
                        '400' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'get' => [
                    'operationId' => 'listReclamaciones',
                    'tags' => ['Reclamaciones'],
                    'summary' => 'Listar reclamaciones',
                    'description' => 'Obtiene la lista de reclamaciones con paginación.',
                    'security' => [['sanctum' => []]],
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
                            'description' => 'Lista de reclamaciones',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ReclamacionListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/reclamaciones/{id}' => [
                'get' => [
                    'operationId' => 'getReclamacion',
                    'tags' => ['Reclamaciones'],
                    'summary' => 'Obtener detalles de reclamación',
                    'description' => 'Retorna los detalles completos de una reclamación específica.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la reclamación']
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Detalles de la reclamación',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ReclamacionDetailResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Reclamación no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'put' => [
                    'operationId' => 'updateReclamacion',
                    'tags' => ['Reclamaciones'],
                    'summary' => 'Actualizar estado de reclamación',
                    'description' => 'Actualiza el estado de una reclamación (marcar como atendida).',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la reclamación']
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ReclamacionUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Estado actualizado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para editar reclamaciones'],
                        '404' => ['description' => 'Reclamación no encontrada'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteReclamacion',
                    'tags' => ['Reclamaciones'],
                    'summary' => 'Eliminar reclamación',
                    'description' => 'Elimina una reclamación del sistema.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la reclamación']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Reclamación eliminada exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para eliminar reclamaciones'],
                        '404' => ['description' => 'Reclamación no encontrada'],
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
        $this->info('✅ Documentación de Reclamaciones actualizada exitosamente');

        return 0;
    }
}
