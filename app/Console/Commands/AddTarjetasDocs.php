<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddTarjetasDocs extends Command
{
    protected $signature = 'docs:add-tarjetas';
    protected $description = 'Agregar documentación Swagger para endpoints de tarjetas';

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
            'TarjetaCreateRequest' => [
                'type' => 'object',
                'title' => 'Tarjeta Create Request',
                'required' => ['titulo', 'descripcion', 'id_blog_body'],
                'properties' => [
                    'titulo' => ['type' => 'string', 'example' => 'Título de la tarjeta', 'maxLength' => 140],
                    'descripcion' => ['type' => 'string', 'example' => 'Descripción detallada de la tarjeta'],
                    'enlace' => ['type' => 'string', 'nullable' => true, 'example' => 'https://example.com'],
                    'palabra' => ['type' => 'string', 'nullable' => true, 'example' => 'clave'],
                    'id_blog_body' => ['type' => 'integer', 'example' => 1],
                ]
            ],
            'TarjetaUpdateRequest' => [
                'type' => 'object',
                'title' => 'Tarjeta Update Request',
                'required' => ['titulo', 'descripcion', 'id_blog_body'],
                'properties' => [
                    'titulo' => ['type' => 'string', 'example' => 'Título actualizado', 'maxLength' => 140],
                    'descripcion' => ['type' => 'string', 'example' => 'Descripción actualizada'],
                    'enlace' => ['type' => 'string', 'nullable' => true],
                    'palabra' => ['type' => 'string', 'nullable' => true],
                    'id_blog_body' => ['type' => 'integer', 'example' => 1],
                ]
            ],
            'TarjetaResponse' => [
                'type' => 'object',
                'title' => 'Tarjeta Response',
                'properties' => [
                    'id_tarjeta' => ['type' => 'integer', 'example' => 1],
                    'titulo' => ['type' => 'string', 'example' => 'Título de la tarjeta'],
                    'descripcion' => ['type' => 'string', 'example' => 'Descripción detallada'],
                    'enlace' => ['type' => 'string', 'nullable' => true],
                    'palabra' => ['type' => 'string', 'nullable' => true],
                    'id_blog_body' => ['type' => 'integer'],
                ]
            ],
            'ConsejoCreateRequest' => [
                'type' => 'object',
                'title' => 'Consejo Create Request',
                'required' => ['texto', 'id_blog_body'],
                'properties' => [
                    'texto' => ['type' => 'string', 'example' => 'Opta por colores que reflejen la personalidad de tu bar.'],
                    'enlace' => ['type' => 'string', 'nullable' => true, 'example' => 'https://example.com'],
                    'palabra' => ['type' => 'string', 'nullable' => true, 'example' => 'clave'],
                    'orden' => ['type' => 'integer', 'nullable' => true, 'example' => 0],
                    'id_blog_body' => ['type' => 'integer', 'example' => 1],
                ]
            ],
            'ConsejoUpdateRequest' => [
                'type' => 'object',
                'title' => 'Consejo Update Request',
                'required' => ['texto', 'id_blog_body'],
                'properties' => [
                    'texto' => ['type' => 'string', 'example' => 'Texto actualizado'],
                    'enlace' => ['type' => 'string', 'nullable' => true],
                    'palabra' => ['type' => 'string', 'nullable' => true],
                    'orden' => ['type' => 'integer', 'nullable' => true],
                    'id_blog_body' => ['type' => 'integer', 'example' => 1],
                ]
            ],
            'ConsejoResponse' => [
                'type' => 'object',
                'title' => 'Consejo Response',
                'properties' => [
                    'id_consejo' => ['type' => 'integer', 'example' => 1],
                    'texto' => ['type' => 'string', 'example' => 'Opta por colores que reflejen la personalidad de tu bar.'],
                    'enlace' => ['type' => 'string', 'nullable' => true],
                    'palabra' => ['type' => 'string', 'nullable' => true],
                    'orden' => ['type' => 'integer'],
                    'id_blog_body' => ['type' => 'integer'],
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
            '/tarjeta' => [
                'post' => [
                    'operationId' => 'createTarjeta',
                    'tags' => ['Tarjetas'],
                    'summary' => 'Crear tarjeta',
                    'description' => 'Crea una nueva tarjeta dentro de un blog body. Solo usuarios con permiso crear-tarjetas.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/TarjetaCreateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Tarjeta creada correctamente'],
                        '400' => ['description' => 'Error de validación'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para crear tarjetas'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/tarjeta/{id}' => [
                'put' => [
                    'operationId' => 'updateTarjeta',
                    'tags' => ['Tarjetas'],
                    'summary' => 'Actualizar tarjeta',
                    'description' => 'Actualiza la información de una tarjeta existente.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la tarjeta']
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/TarjetaUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Tarjeta actualizada correctamente'],
                        '400' => ['description' => 'Error de validación'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Tarjeta no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/tarjetas_delete/{id}' => [
                'delete' => [
                    'operationId' => 'deleteTarjetas',
                    'tags' => ['Tarjetas'],
                    'summary' => 'Eliminar tarjetas por blog_body',
                    'description' => 'Elimina todas las tarjetas asociadas a un blog_body específico.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del blog_body']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Tarjetas eliminadas correctamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para eliminar tarjetas'],
                        '404' => ['description' => 'No se encontraron tarjetas'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/consejo' => [
                'post' => [
                    'operationId' => 'createConsejo',
                    'tags' => ['Consejos'],
                    'summary' => 'Crear consejo',
                    'description' => 'Crea un nuevo consejo dentro de un blog body. Solo usuarios con permiso crear-tarjetas.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ConsejoCreateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Consejo creado correctamente'],
                        '400' => ['description' => 'Error de validación'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para crear tarjetas'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/consejo/{id}' => [
                'put' => [
                    'operationId' => 'updateConsejo',
                    'tags' => ['Consejos'],
                    'summary' => 'Actualizar consejo',
                    'description' => 'Actualiza la información de un consejo existente.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del consejo']
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ConsejoUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Consejo actualizado correctamente'],
                        '400' => ['description' => 'Error de validación'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Consejo no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteConsejo',
                    'tags' => ['Consejos'],
                    'summary' => 'Eliminar consejo',
                    'description' => 'Elimina un consejo del sistema.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del consejo']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Consejo eliminado correctamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para eliminar tarjetas'],
                        '404' => ['description' => 'Consejo no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/consejos/{id}' => [
                'get' => [
                    'operationId' => 'listConsejos',
                    'tags' => ['Consejos'],
                    'summary' => 'Listar consejos de un blog body',
                    'description' => 'Obtiene todos los consejos asociados a un blog body.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del blog_body']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Lista de consejos'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'No se encontraron consejos'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/consejos_delete/{id}' => [
                'delete' => [
                    'operationId' => 'deleteConsejos',
                    'tags' => ['Consejos'],
                    'summary' => 'Eliminar consejos por blog_body',
                    'description' => 'Elimina todos los consejos asociados a un blog_body específico.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID del blog_body']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Consejos eliminados correctamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para eliminar tarjetas'],
                        '404' => ['description' => 'No se encontraron consejos'],
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
        $this->info('✅ Documentación de Tarjetas actualizada exitosamente');

        return 0;
    }
}
