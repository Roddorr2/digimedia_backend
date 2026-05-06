<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddContactosDocs extends Command
{
    protected $signature = 'docs:add-contactos';
    protected $description = 'Agregar documentación Swagger para endpoints de contactos';

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
            'ContactoCreateRequest' => [
                'type' => 'object',
                'title' => 'Contacto Create Request',
                'required' => ['nombre', 'email', 'numero', 'mensaje'],
                'properties' => [
                    'nombre' => ['type' => 'string', 'example' => 'Juan Pérez', 'maxLength' => 255],
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'juan@example.com', 'maxLength' => 255],
                    'numero' => ['type' => 'string', 'example' => '+51987654321', 'maxLength' => 20],
                    'mensaje' => ['type' => 'string', 'example' => 'Quisiera consultar sobre los servicios disponibles', 'maxLength' => 1050],
                ]
            ],
            'ContactoUpdateRequest' => [
                'type' => 'object',
                'title' => 'Contacto Update Request',
                'required' => ['estado'],
                'properties' => [
                    'estado' => ['type' => 'boolean', 'example' => true, 'description' => 'Marca si el contacto ha sido atendido'],
                ]
            ],
            'ContactoResponse' => [
                'type' => 'object',
                'title' => 'Contacto Response',
                'properties' => [
                    'id_contactanos' => ['type' => 'integer', 'example' => 1],
                    'nombre' => ['type' => 'string', 'example' => 'Juan Pérez'],
                    'email' => ['type' => 'string', 'example' => 'juan@example.com'],
                    'numero' => ['type' => 'string', 'example' => '+51987654321'],
                    'mensaje' => ['type' => 'string', 'example' => 'Quisiera consultar sobre los servicios disponibles'],
                    'fecha' => ['type' => 'string', 'format' => 'date-time', 'example' => '2026-04-21 10:30:00'],
                    'estado' => ['type' => 'boolean', 'example' => false],
                ]
            ],
            'ContactoListResponse' => [
                'type' => 'object',
                'title' => 'Contacto List Response',
                'properties' => [
                    'data' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/ContactoResponse']],
                    'current_page' => ['type' => 'integer', 'example' => 1],
                    'from' => ['type' => 'integer', 'example' => 1],
                    'last_page' => ['type' => 'integer', 'example' => 5],
                    'per_page' => ['type' => 'integer', 'example' => 4],
                    'to' => ['type' => 'integer', 'example' => 4],
                    'total' => ['type' => 'integer', 'example' => 20],
                ]
            ],
            'ContactoDetailResponse' => [
                'type' => 'object',
                'title' => 'Contacto Detail Response',
                'properties' => [
                    'status' => ['type' => 'string', 'example' => 'success'],
                    'data' => ['$ref' => '#/components/schemas/ContactoResponse'],
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
            '/contactanos' => [
                'post' => [
                    'operationId' => 'createContacto',
                    'tags' => ['Contactos'],
                    'summary' => 'Crear nuevo contacto',
                    'description' => 'Envía una solicitud de contacto. Este endpoint es público y no requiere autenticación.',
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ContactoCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Contacto guardado exitosamente'],
                        '400' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'get' => [
                    'operationId' => 'listContactos',
                    'tags' => ['Contactos'],
                    'summary' => 'Listar contactos',
                    'description' => 'Obtiene la lista de contactos recibidos con paginación.',
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
                            'description' => 'Lista de contactos',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ContactoListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/contactanos/{id}' => [
                'get' => [
                    'operationId' => 'getContacto',
                    'tags' => ['Contactos'],
                    'summary' => 'Obtener detalles de contacto',
                    'description' => 'Retorna los detalles completos de un contacto específico.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del contacto']
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Detalles del contacto',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ContactoDetailResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Contacto no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'put' => [
                    'operationId' => 'updateContacto',
                    'tags' => ['Contactos'],
                    'summary' => 'Actualizar estado de contacto',
                    'description' => 'Actualiza el estado de un contacto (marcado como atendido o no).',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del contacto']
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ContactoUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Estado actualizado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para editar contactos'],
                        '404' => ['description' => 'Contacto no encontrado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteContacto',
                    'tags' => ['Contactos'],
                    'summary' => 'Eliminar contacto',
                    'description' => 'Elimina un contacto del sistema.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del contacto']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Contacto eliminado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para eliminar contactos'],
                        '404' => ['description' => 'Contacto no encontrado'],
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
        $this->info('✅ Documentación de Contactos actualizada exitosamente');

        return 0;
    }
}
