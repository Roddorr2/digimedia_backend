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
            'CommendTarjetaCreateRequest' => [
                'type' => 'object',
                'title' => 'Commend Tarjeta Create Request',
                'properties' => [
                    'titulo' => ['type' => 'string', 'nullable' => true, 'example' => 'Título de recomendación', 'maxLength' => 255],
                    'texto1' => ['type' => 'string', 'nullable' => true, 'example' => 'Primer texto', 'maxLength' => 255],
                    'texto2' => ['type' => 'string', 'nullable' => true, 'example' => 'Segundo texto', 'maxLength' => 255],
                    'texto3' => ['type' => 'string', 'nullable' => true, 'example' => 'Tercer texto', 'maxLength' => 255],
                    'texto4' => ['type' => 'string', 'nullable' => true, 'example' => 'Cuarto texto', 'maxLength' => 255],
                    'texto5' => ['type' => 'string', 'nullable' => true, 'example' => 'Quinto texto', 'maxLength' => 255],
                ]
            ],
            'CommendTarjetaUpdateRequest' => [
                'type' => 'object',
                'title' => 'Commend Tarjeta Update Request',
                'required' => ['titulo', 'texto1', 'texto2', 'texto3'],
                'properties' => [
                    'titulo' => ['type' => 'string', 'example' => 'Título actualizado', 'maxLength' => 255],
                    'texto1' => ['type' => 'string', 'example' => 'Primer texto', 'maxLength' => 255],
                    'texto2' => ['type' => 'string', 'example' => 'Segundo texto', 'maxLength' => 255],
                    'texto3' => ['type' => 'string', 'example' => 'Tercer texto', 'maxLength' => 255],
                    'texto4' => ['type' => 'string', 'nullable' => true, 'example' => 'Cuarto texto', 'maxLength' => 255],
                    'texto5' => ['type' => 'string', 'nullable' => true, 'example' => 'Quinto texto', 'maxLength' => 255],
                ]
            ],
            'CommendTarjetaResponse' => [
                'type' => 'object',
                'title' => 'Commend Tarjeta Response',
                'properties' => [
                    'id_commend_tarjeta' => ['type' => 'integer', 'example' => 1],
                    'titulo' => ['type' => 'string', 'example' => 'Título de recomendación'],
                    'texto1' => ['type' => 'string', 'example' => 'Primer texto'],
                    'texto2' => ['type' => 'string', 'example' => 'Segundo texto'],
                    'texto3' => ['type' => 'string', 'example' => 'Tercer texto'],
                    'texto4' => ['type' => 'string', 'nullable' => true],
                    'texto5' => ['type' => 'string', 'nullable' => true],
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
            '/commend_tarjeta' => [
                'post' => [
                    'operationId' => 'createCommendTarjeta',
                    'tags' => ['Tarjetas Recomendadas'],
                    'summary' => 'Crear tarjeta recomendada',
                    'description' => 'Crea una nueva tarjeta recomendada en el sistema.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CommendTarjetaCreateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'CommendTarjeta creada correctamente'],
                        '400' => ['description' => 'Error de validación'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para crear tarjetas'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ]
            ],
            '/commend_tarjeta/{id}' => [
                'put' => [
                    'operationId' => 'updateCommendTarjeta',
                    'tags' => ['Tarjetas Recomendadas'],
                    'summary' => 'Actualizar tarjeta recomendada',
                    'description' => 'Actualiza la información de una tarjeta recomendada.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la tarjeta recomendada']
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CommendTarjetaUpdateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Tarjeta actualizada'],
                        '400' => ['description' => 'Error de validación'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso'],
                        '404' => ['description' => 'Tarjeta no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteCommendTarjeta',
                    'tags' => ['Tarjetas Recomendadas'],
                    'summary' => 'Eliminar tarjeta recomendada',
                    'description' => 'Elimina una tarjeta recomendada del sistema.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID de la tarjeta recomendada']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Tarjeta eliminada correctamente'],
                        '401' => ['description' => 'No autenticado'],
                        '403' => ['description' => 'Sin permiso para eliminar tarjetas'],
                        '404' => ['description' => 'Tarjeta no encontrada'],
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
