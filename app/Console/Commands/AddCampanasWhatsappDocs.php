<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddCampanasWhatsappDocs extends Command
{
    protected $signature = 'docs:add-campanas-whatsapp';
    protected $description = 'Agregar documentación Swagger para endpoints de campañas WhatsApp';

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
            'CampanaCreateRequest' => [
                'type' => 'object',
                'title' => 'Campaña Crear Request',
                'required' => ['service', 'paragraph', 'image'],
                'properties' => [
                    'service' => ['type' => 'string', 'enum' => ['p1', 'p2', 'p3', 'p4'], 'example' => 'p1', 'description' => 'Código de servicio: p1=Web, p2=Redes, p3=Marketing, p4=Branding'],
                    'paragraph' => ['type' => 'string', 'example' => 'Mensaje de la campaña...', 'minLength' => 10, 'maxLength' => 1000],
                    'image' => ['type' => 'string', 'format' => 'binary', 'description' => 'Imagen (jpg, jpeg, png, webp, max 2MB)'],
                ]
            ],
            'CampanaCreateResponse' => [
                'type' => 'object',
                'title' => 'Campaña Create Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'message' => ['type' => 'string', 'example' => 'Campaña creada exitosamente en borrador'],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'campania_id' => ['type' => 'integer', 'example' => 1],
                            'total_destinatarios' => ['type' => 'integer', 'example' => 150],
                            'estado' => ['type' => 'string', 'example' => 'borrador'],
                            'servicio' => ['type' => 'string', 'example' => 'Diseño y Desarrollo Web'],
                        ]
                    ]
                ]
            ],
            'CampanaPreviewResponse' => [
                'type' => 'object',
                'title' => 'Campaña Preview Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'service' => ['type' => 'string', 'example' => 'p1'],
                            'total_destinatarios' => ['type' => 'integer', 'example' => 150],
                        ]
                    ]
                ]
            ],
            'CampanaStatusResponse' => [
                'type' => 'object',
                'title' => 'Campaña Status Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'id_campania' => ['type' => 'integer', 'example' => 1],
                            'servicio' => ['type' => 'string', 'example' => 'Diseño y Desarrollo Web'],
                            'estado' => ['type' => 'string', 'enum' => ['borrador', 'pendiente', 'activa', 'pausada', 'completada', 'cancelada'], 'example' => 'activa'],
                            'progreso' => [
                                'type' => 'object',
                                'properties' => [
                                    'total' => ['type' => 'integer', 'example' => 150],
                                    'exitosos' => ['type' => 'integer', 'example' => 120],
                                    'fallidos' => ['type' => 'integer', 'example' => 10],
                                    'pendientes' => ['type' => 'integer', 'example' => 20],
                                    'porcentaje' => ['type' => 'number', 'example' => 80.5],
                                ]
                            ],
                            'envios_hoy' => ['type' => 'integer', 'example' => 50],
                            'limite_diario' => ['type' => 'integer', 'example' => 50],
                            'fechas' => [
                                'type' => 'object',
                                'properties' => [
                                    'inicio' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                                    'fin' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                                    'creacion' => ['type' => 'string', 'format' => 'date-time'],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'CampanaListResponse' => [
                'type' => 'object',
                'title' => 'Campaña List Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'active_campaign' => [
                        'type' => 'object',
                        'nullable' => true,
                        'properties' => [
                            'id_campania' => ['type' => 'integer'],
                            'servicio' => ['type' => 'string'],
                            'estado' => ['type' => 'string'],
                            'progreso' => ['type' => 'number'],
                            'envios_hoy' => ['type' => 'integer'],
                        ]
                    ],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'campanias' => ['type' => 'array', 'items' => ['type' => 'object']]
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
            '/whatsapp/campaign/preview/{service}' => [
                'get' => [
                    'operationId' => 'previewCampaign',
                    'tags' => ['Campañas WhatsApp'],
                    'summary' => 'Vista previa de destinatarios',
                    'description' => 'Obtiene el total de destinatarios para un servicio sin crear campaña. Útil para preview antes de crear.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'service',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'string', 'enum' => ['p1', 'p2', 'p3', 'p4']],
                            'description' => 'Código de servicio (p1=Web, p2=Redes, p3=Marketing, p4=Branding)'
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Información de destinatarios',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CampanaPreviewResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Servicio inválido'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/whatsapp/campaign/create' => [
                'post' => [
                    'operationId' => 'createCampaign',
                    'tags' => ['Campañas WhatsApp'],
                    'summary' => 'Crear campaña WhatsApp',
                    'description' => 'Crea una nueva campaña WhatsApp en estado BORRADOR. Solo usuarios con permiso crear-campañas.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'multipart/form-data' => ['schema' => ['$ref' => '#/components/schemas/CampanaCreateRequest']]
                        ],
                    ],
                    'responses' => [
                        '201' => [
                            'description' => 'Campaña creada exitosamente',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CampanaCreateResponse']]],
                        ],
                        '400' => ['description' => 'No hay destinatarios disponibles'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para crear campañas'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/whatsapp/campaign/{id}/start' => [
                'post' => [
                    'operationId' => 'startCampaign',
                    'tags' => ['Campañas WhatsApp'],
                    'summary' => 'Iniciar campaña WhatsApp',
                    'description' => 'Inicia una campaña desde estado BORRADOR o PAUSADA. Requiere que WhatsApp esté conectado y valida que no haya otra campaña activa (FIFO).',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la campaña']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Campaña iniciada exitosamente'],
                        '400' => ['description' => 'WhatsApp no conectado o no hay destinatarios disponibles'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Campaña no encontrada'],
                        '409' => ['description' => 'Ya hay otra campaña activa (requiere FIFO)'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/whatsapp/campaign/{id}/status' => [
                'get' => [
                    'operationId' => 'getCampaignStatus',
                    'tags' => ['Campañas WhatsApp'],
                    'summary' => 'Obtener estado de campaña',
                    'description' => 'Retorna información detallada del estado de una campaña, incluyendo progreso, envíos exitosos/fallidos y límites diarios.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la campaña']
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Estado de la campaña',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CampanaStatusResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Campaña no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/whatsapp/campaigns' => [
                'get' => [
                    'operationId' => 'listCampaigns',
                    'tags' => ['Campañas WhatsApp'],
                    'summary' => 'Listar campañas WhatsApp',
                    'description' => 'Obtiene un listado de campañas recientes con información de la campaña activa.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'limit',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'default' => 10],
                            'description' => 'Cantidad máxima de campañas a retornar'
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Listado de campañas',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CampanaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
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
        $this->info('✅ Documentación de Campañas WhatsApp actualizada exitosamente');

        return 0;
    }
}
