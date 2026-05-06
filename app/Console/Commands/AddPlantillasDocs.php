<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddPlantillasDocs extends Command
{
    protected $signature = 'docs:add-plantillas';
    protected $description = 'Agregar documentación Swagger para endpoints de plantillas';

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
            'PlantillaWhatsappResponse' => [
                'type' => 'object',
                'title' => 'Plantilla Whatsapp Response',
                'properties' => [
                    'id_plantilla_whatsapp' => ['type' => 'integer', 'example' => 1],
                    'id_servicio' => ['type' => 'integer', 'example' => 1],
                    'numero_plantilla' => ['type' => 'integer', 'example' => 1],
                    'nombre' => ['type' => 'string', 'example' => 'Bienvenida'],
                    'mensaje' => ['type' => 'string', 'example' => 'Hola {{nombre}}, bienvenido a nuestro servicio'],
                    'imagen_url' => ['type' => 'string', 'nullable' => true, 'example' => 'https://example.com/imagen.jpg'],
                    'created_by' => ['type' => 'integer', 'nullable' => true],
                    'updated_by' => ['type' => 'integer', 'nullable' => true],
                ]
            ],
            'PlantillaEmailResponse' => [
                'type' => 'object',
                'title' => 'Plantilla Email Response',
                'properties' => [
                    'id_plantilla_email' => ['type' => 'integer', 'example' => 1],
                    'id_servicio' => ['type' => 'integer', 'example' => 1],
                    'numero_plantilla' => ['type' => 'integer', 'example' => 1],
                    'nombre' => ['type' => 'string', 'example' => 'Bienvenida'],
                    'asunto' => ['type' => 'string', 'example' => 'Bienvenido a {{nombre_servicio}}'],
                    'cuerpo' => ['type' => 'string', 'example' => '<html><body>Hola {{nombre}}</body></html>'],
                    'created_by' => ['type' => 'integer', 'nullable' => true],
                    'updated_by' => ['type' => 'integer', 'nullable' => true],
                ]
            ],
            'PlantillaWhatsappUpdateRequest' => [
                'type' => 'object',
                'title' => 'Plantilla Whatsapp Update Request',
                'required' => ['mensaje'],
                'properties' => [
                    'mensaje' => ['type' => 'string', 'example' => 'Nuevo mensaje de la plantilla', 'maxLength' => 5000],
                    'imagen' => ['type' => 'string', 'format' => 'binary', 'nullable' => true, 'description' => 'Archivo de imagen (jpg, jpeg, png, webp, max 5MB)'],
                ]
            ],
            'PlantillaEmailUpdateRequest' => [
                'type' => 'object',
                'title' => 'Plantilla Email Update Request',
                'required' => ['asunto', 'cuerpo'],
                'properties' => [
                    'asunto' => ['type' => 'string', 'example' => 'Asunto del email', 'maxLength' => 255],
                    'cuerpo' => ['type' => 'string', 'example' => '<html><body>Contenido del email</body></html>'],
                ]
            ],
            'PlantillaListResponse' => [
                'type' => 'object',
                'title' => 'Plantilla List Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => ['type' => 'array', 'items' => ['type' => 'object']],
                ]
            ],
            'PlantillaDetailResponse' => [
                'type' => 'object',
                'title' => 'Plantilla Detail Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => ['type' => 'object'],
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
            '/plantillas/whatsapp' => [
                'get' => [
                    'operationId' => 'listPlantillasWhatsapp',
                    'tags' => ['Plantillas'],
                    'summary' => 'Listar plantillas WhatsApp',
                    'description' => 'Obtiene todas las plantillas de WhatsApp disponibles con sus servicios asociados.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de plantillas WhatsApp',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PlantillaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/plantillas/whatsapp/{id}' => [
                'get' => [
                    'operationId' => 'getPlantillaWhatsapp',
                    'tags' => ['Plantillas'],
                    'summary' => 'Obtener plantilla WhatsApp',
                    'description' => 'Retorna los detalles de una plantilla WhatsApp específica.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la plantilla']
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Detalles de la plantilla WhatsApp',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PlantillaDetailResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Plantilla no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/plantillas/whatsapp/{id}/actualizar' => [
                'post' => [
                    'operationId' => 'updatePlantillaWhatsapp',
                    'tags' => ['Plantillas'],
                    'summary' => 'Actualizar plantilla WhatsApp',
                    'description' => 'Actualiza el mensaje e imagen de una plantilla WhatsApp. Solo admin y marketing.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la plantilla']
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => ['schema' => ['$ref' => '#/components/schemas/PlantillaWhatsappUpdateRequest']]
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Plantilla actualizada exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Plantilla no encontrada'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/plantillas/email' => [
                'get' => [
                    'operationId' => 'listPlantillasEmail',
                    'tags' => ['Plantillas'],
                    'summary' => 'Listar plantillas Email',
                    'description' => 'Obtiene todas las plantillas de Email disponibles con sus servicios asociados.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de plantillas Email',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PlantillaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/plantillas/email/{id}' => [
                'get' => [
                    'operationId' => 'getPlantillaEmail',
                    'tags' => ['Plantillas'],
                    'summary' => 'Obtener plantilla Email',
                    'description' => 'Retorna los detalles de una plantilla Email específica.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la plantilla']
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Detalles de la plantilla Email',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PlantillaDetailResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Plantilla no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/plantillas/email/{id}/actualizar' => [
                'post' => [
                    'operationId' => 'updatePlantillaEmail',
                    'tags' => ['Plantillas'],
                    'summary' => 'Actualizar plantilla Email',
                    'description' => 'Actualiza el asunto y cuerpo de una plantilla Email. Solo admin y marketing.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la plantilla']
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PlantillaEmailUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Plantilla actualizada exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Plantilla no encontrada'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/plantillas/whatsapp/{id_servicio}/{numero_plantilla}' => [
                'get' => [
                    'operationId' => 'getPlantillaWhatsappByServicioNumero',
                    'tags' => ['Plantillas'],
                    'summary' => 'Obtener plantilla WhatsApp por servicio y número',
                    'description' => 'Obtiene una plantilla WhatsApp específica usando servicio y número. Endpoint público con API Key para whatsapp-service.',
                    'security' => [['api_key' => []]],
                    'parameters' => [
                        ['name' => 'id_servicio', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del servicio'],
                        ['name' => 'numero_plantilla', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'Número de la plantilla'],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Plantilla WhatsApp encontrada',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PlantillaDetailResponse']]],
                        ],
                        '404' => ['description' => 'Plantilla no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/plantillas/email/{id_servicio}/{numero_plantilla}' => [
                'get' => [
                    'operationId' => 'getPlantillaEmailByServicioNumero',
                    'tags' => ['Plantillas'],
                    'summary' => 'Obtener plantilla Email por servicio y número',
                    'description' => 'Obtiene una plantilla Email específica usando servicio y número. Endpoint público con API Key para Jobs y servicios externos.',
                    'security' => [['api_key' => []]],
                    'parameters' => [
                        ['name' => 'id_servicio', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del servicio'],
                        ['name' => 'numero_plantilla', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'Número de la plantilla'],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Plantilla Email encontrada',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PlantillaDetailResponse']]],
                        ],
                        '404' => ['description' => 'Plantilla no encontrada'],
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
        $this->info('✅ Documentación de Plantillas actualizada exitosamente');

        return 0;
    }
}
