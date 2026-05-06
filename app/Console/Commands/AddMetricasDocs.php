<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddMetricasDocs extends Command
{
    protected $signature = 'docs:add-metricas';
    protected $description = 'Agregar documentación Swagger para endpoints de métricas';

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
            'MetricaBlogResponse' => [
                'type' => 'object',
                'title' => 'Métrica Blog Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'data' => ['type' => 'object'],
                ]
            ],
            'MetricaCountResponse' => [
                'type' => 'object',
                'title' => 'Métrica Count Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'count' => ['type' => 'integer', 'example' => 45],
                ]
            ],
            'MetricaListResponse' => [
                'type' => 'object',
                'title' => 'Métrica List Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'data' => ['type' => 'array', 'items' => ['type' => 'object']],
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
            '/metrics/count_blogs_by_month' => [
                'get' => [
                    'operationId' => 'countBlogsByMonth',
                    'tags' => ['Métricas'],
                    'summary' => 'Cantidad de blogs por mes',
                    'description' => 'Obtiene la cantidad de blogs creados en un mes específico.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'month',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                            'description' => 'Número del mes (1-12). Si no se proporciona usa el mes actual.'
                        ],
                        [
                            'name' => 'year',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 2000],
                            'description' => 'Año. Si no se proporciona usa el año actual.'
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Cantidad de blogs del mes',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaBlogResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Validación de parámetros fallida'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/list_blogs_by_months_12' => [
                'get' => [
                    'operationId' => 'listBlogsByMonths12',
                    'tags' => ['Métricas'],
                    'summary' => 'Blogs últimos 12 meses',
                    'description' => 'Lista cantidad de blogs creados en cada mes de los últimos 12 meses.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de blogs por mes',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/top5_months_with_more_blogs' => [
                'get' => [
                    'operationId' => 'top5MonthsWithMoreBlogs',
                    'tags' => ['Métricas'],
                    'summary' => 'Top 5 meses con más blogs',
                    'description' => 'Retorna los 5 meses con mayor cantidad de blogs creados en toda la historia.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Top 5 meses',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/cards_by_plantilla' => [
                'get' => [
                    'operationId' => 'cardsByPlantilla',
                    'tags' => ['Métricas'],
                    'summary' => 'Cards por plantilla',
                    'description' => 'Lista cards creadas en una plantilla específica durante un mes.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id_plantilla',
                            'in' => 'query',
                            'required' => true,
                            'schema' => ['type' => 'integer', 'minimum' => 1],
                            'description' => 'ID de la plantilla'
                        ],
                        [
                            'name' => 'month',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                            'description' => 'Número del mes (1-12)'
                        ],
                        [
                            'name' => 'year',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 2000],
                            'description' => 'Año'
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de cards',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Validación de parámetros fallida'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/count_cards_by_plantilla' => [
                'get' => [
                    'operationId' => 'countCardsByPlantilla',
                    'tags' => ['Métricas'],
                    'summary' => 'Cantidad de cards por plantilla',
                    'description' => 'Obtiene la cantidad de cards creadas en una plantilla durante un mes específico.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id_plantilla',
                            'in' => 'query',
                            'required' => true,
                            'schema' => ['type' => 'integer', 'minimum' => 1],
                            'description' => 'ID de la plantilla'
                        ],
                        [
                            'name' => 'month',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                        ],
                        [
                            'name' => 'year',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 2000],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Cantidad de cards',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaCountResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Validación de parámetros fallida'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/count_total_cards' => [
                'get' => [
                    'operationId' => 'countTotalCards',
                    'tags' => ['Métricas'],
                    'summary' => 'Total de cards por plantilla',
                    'description' => 'Tabla con cantidad total de cards creadas por cada plantilla en un mes.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'month',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                        ],
                        [
                            'name' => 'year',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 2000],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Tabla de totales',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Validación de parámetros fallida'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/list_empleado_cards' => [
                'get' => [
                    'operationId' => 'listEmpleadoCards',
                    'tags' => ['Métricas'],
                    'summary' => 'Cards por empleado',
                    'description' => 'Lista cards creadas por un empleado específico en un mes.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id_empleado',
                            'in' => 'query',
                            'required' => true,
                            'schema' => ['type' => 'integer', 'minimum' => 1],
                            'description' => 'ID del empleado'
                        ],
                        [
                            'name' => 'month',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                        ],
                        [
                            'name' => 'year',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 2000],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de cards del empleado',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Validación de parámetros fallida'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/count_cards_by_empleado' => [
                'get' => [
                    'operationId' => 'countCardsByEmpleado',
                    'tags' => ['Métricas'],
                    'summary' => 'Cantidad de cards por empleado',
                    'description' => 'Obtiene la cantidad de cards creadas por un empleado en un mes específico.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'id_empleado',
                            'in' => 'query',
                            'required' => true,
                            'schema' => ['type' => 'integer', 'minimum' => 1],
                            'description' => 'ID del empleado'
                        ],
                        [
                            'name' => 'month',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                        ],
                        [
                            'name' => 'year',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 2000],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Cantidad de cards',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaCountResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Validación de parámetros fallida'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/count_total_cards_by_empleado' => [
                'get' => [
                    'operationId' => 'countTotalCardsByEmpleado',
                    'tags' => ['Métricas'],
                    'summary' => 'Total de cards por empleado',
                    'description' => 'Tabla con cantidad total de cards creadas por cada empleado en un mes.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'month',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                        ],
                        [
                            'name' => 'year',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 2000],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Tabla de totales por empleado',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Validación de parámetros fallida'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/frecuencia_publicacion_cards_todos_empleados' => [
                'get' => [
                    'operationId' => 'frecuenciaPublicacionCards',
                    'tags' => ['Métricas'],
                    'summary' => 'Frecuencia de publicación mensual',
                    'description' => 'Obtiene la frecuencia de publicación de cards (promedio mensual) de todos los empleados.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Frecuencia de publicación por empleado',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/metrics/tiempo_creacion_edicion_cards' => [
                'get' => [
                    'operationId' => 'tiempoCreacionEdicion',
                    'tags' => ['Métricas'],
                    'summary' => 'Tiempo creación a edición',
                    'description' => 'Calcula el tiempo en minutos que transcurre entre la creación y la primera edición de cards en un mes.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        [
                            'name' => 'month',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 12],
                        ],
                        [
                            'name' => 'year',
                            'in' => 'query',
                            'schema' => ['type' => 'integer', 'minimum' => 2000],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Tiempos de creación a edición',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/MetricaListResponse']]],
                        ],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Validación de parámetros fallida'],
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
        $this->info('✅ Documentación de Métricas actualizada exitosamente');

        return 0;
    }
}
