<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddBlogsDocs extends Command
{
    protected $signature = 'docs:add-blogs';
    protected $description = 'Agregar documentación Swagger para endpoints de blogs y cards';

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
            'BlogCreateRequest' => [
                'type' => 'object',
                'title' => 'Blog Create Request',
                'required' => ['titulo', 'contenido'],
                'properties' => [
                    'titulo' => ['type' => 'string', 'example' => 'Mi Primer Blog'],
                    'contenido' => ['type' => 'string', 'example' => 'Contenido del blog'],
                    'estado' => ['type' => 'string', 'enum' => ['activo', 'inactivo'], 'example' => 'activo'],
                ]
            ],
            'BlogResponse' => [
                'type' => 'object',
                'title' => 'Blog Response',
                'properties' => [
                    'id_blog' => ['type' => 'integer'],
                    'titulo' => ['type' => 'string'],
                    'contenido' => ['type' => 'string'],
                    'estado' => ['type' => 'string'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at' => ['type' => 'string', 'format' => 'date-time'],
                ]
            ],
            'CardCreateRequest' => [
                'type' => 'object',
                'title' => 'Card Create Request',
                'properties' => [
                    'titulo' => ['type' => 'string', 'example' => 'Titulo de la tarjeta'],
                    'descripcion' => ['type' => 'string', 'example' => 'Descripción breve'],
                    'contenido' => ['type' => 'string'],
                ]
            ],
            'CardResponse' => [
                'type' => 'object',
                'title' => 'Card Response',
                'properties' => [
                    'id_card' => ['type' => 'integer'],
                    'titulo' => ['type' => 'string'],
                    'descripcion' => ['type' => 'string'],
                    'contenido' => ['type' => 'string'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                ]
            ],
            'BlogHeadCreateRequest' => [
                'type' => 'object',
                'title' => 'Blog Head Create Request',
                'properties' => [
                    'titulo' => ['type' => 'string'],
                    'subtitulo' => ['type' => 'string'],
                    'imagen' => ['type' => 'string', 'format' => 'url'],
                    'id_blog' => ['type' => 'integer'],
                ]
            ],
            'BlogBodyCreateRequest' => [
                'type' => 'object',
                'title' => 'Blog Body Create Request',
                'properties' => [
                    'contenido' => ['type' => 'string'],
                    'id_blog' => ['type' => 'integer'],
                ]
            ],
            'BlogFooterCreateRequest' => [
                'type' => 'object',
                'title' => 'Blog Footer Create Request',
                'properties' => [
                    'autor' => ['type' => 'string'],
                    'fecha' => ['type' => 'string', 'format' => 'date'],
                    'id_blog' => ['type' => 'integer'],
                ]
            ],
            'ImageUploadResponse' => [
                'type' => 'object',
                'title' => 'Image Upload Response',
                'properties' => [
                    'status' => ['type' => 'integer', 'example' => 200],
                    'message' => ['type' => 'string', 'example' => 'Imagen subida exitosamente'],
                    'image_url' => ['type' => 'string', 'format' => 'url'],
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
            '/blogs' => [
                'get' => [
                    'operationId' => 'listBlogs',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Listar blogs públicos',
                    'description' => 'Obtiene la lista de blogs disponibles públicamente.',
                    'responses' => [
                        '200' => [
                            'description' => 'Lista de blogs',
                            'content' => ['application/json' => ['schema' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/BlogResponse']]]],
                        ],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blogs/{id}' => [
                'get' => [
                    'operationId' => 'getBlog',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Obtener detalles de un blog',
                    'description' => 'Retorna los detalles completos de un blog específico.',
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Detalles del blog',
                            'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogResponse']]],
                        ],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteBlog',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Eliminar blog',
                    'description' => 'Elimina un blog del sistema.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Blog eliminado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blogs/links/{link}' => [
                'get' => [
                    'operationId' => 'getBlogByLink',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Obtener blog por slug/link',
                    'description' => 'Obtiene un blog específico usando su slug único.',
                    'parameters' => [
                        ['name' => 'link', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'description' => 'Slug único del blog']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Blog encontrado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blog' => [
                'post' => [
                    'operationId' => 'createBlog',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Crear nuevo blog',
                    'description' => 'Crea un nuevo blog en el sistema.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Blog creado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blog/{id}' => [
                'put' => [
                    'operationId' => 'updateBlog',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Actualizar blog',
                    'description' => 'Actualiza los datos de un blog existente.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogCreateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Blog actualizado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/cards_public' => [
                'get' => [
                    'operationId' => 'listCardsPublic',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Listar cards públicas',
                    'description' => 'Obtiene la lista de todas las cards disponibles públicamente.',
                    'responses' => [
                        '200' => ['description' => 'Lista de cards públicas'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/card' => [
                'post' => [
                    'operationId' => 'createCard',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Crear nueva card',
                    'description' => 'Crea una nueva card (tarjeta de blog) en el sistema.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CardCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Card creada exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/card/{id}' => [
                'put' => [
                    'operationId' => 'updateCard',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Actualizar card',
                    'description' => 'Actualiza los datos de una card existente.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CardCreateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Card actualizada exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Card no encontrada'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/cards/{id}' => [
                'delete' => [
                    'operationId' => 'deleteCard',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Eliminar card',
                    'description' => 'Elimina una card del sistema.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Card eliminada exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Card no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blog_head/{id}' => [
                'get' => [
                    'operationId' => 'getBlogHead',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Obtener encabezado del blog',
                    'description' => 'Obtiene la sección de encabezado de un blog.',
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Encabezado del blog'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'put' => [
                    'operationId' => 'updateBlogHead',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Actualizar encabezado del blog',
                    'description' => 'Actualiza la sección de encabezado de un blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogHeadCreateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Encabezado actualizado'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteBlogHead',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Eliminar encabezado del blog',
                    'description' => 'Elimina la sección de encabezado de un blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Encabezado eliminado'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blog_head' => [
                'post' => [
                    'operationId' => 'createBlogHead',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Crear encabezado del blog',
                    'description' => 'Crea la sección de encabezado de un blog.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogHeadCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Encabezado creado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blog_body/{id}' => [
                'get' => [
                    'operationId' => 'getBlogBody',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Obtener cuerpo del blog',
                    'description' => 'Obtiene la sección de contenido de un blog.',
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Cuerpo del blog'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'put' => [
                    'operationId' => 'updateBlogBody',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Actualizar cuerpo del blog',
                    'description' => 'Actualiza la sección de contenido de un blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogBodyCreateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Cuerpo actualizado'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteBlogBody',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Eliminar cuerpo del blog',
                    'description' => 'Elimina la sección de contenido de un blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Cuerpo eliminado'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blog_body' => [
                'post' => [
                    'operationId' => 'createBlogBody',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Crear cuerpo del blog',
                    'description' => 'Crea la sección de contenido de un blog.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogBodyCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Cuerpo creado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blog_footer/{id}' => [
                'get' => [
                    'operationId' => 'getBlogFooter',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Obtener pie de página del blog',
                    'description' => 'Obtiene la sección de pie de página de un blog.',
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Pie de página del blog'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'put' => [
                    'operationId' => 'updateBlogFooter',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Actualizar pie de página del blog',
                    'description' => 'Actualiza la sección de pie de página de un blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogFooterCreateRequest']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Pie de página actualizado'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
                'delete' => [
                    'operationId' => 'deleteBlogFooter',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Eliminar pie de página del blog',
                    'description' => 'Elimina la sección de pie de página de un blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Pie de página eliminado'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/blog_footer' => [
                'post' => [
                    'operationId' => 'createBlogFooter',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Crear pie de página del blog',
                    'description' => 'Crea la sección de pie de página de un blog.',
                    'security' => [['sanctum' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/BlogFooterCreateRequest']]],
                    ],
                    'responses' => [
                        '201' => ['description' => 'Pie de página creado exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '422' => ['description' => 'Error de validación'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/card/blog/{id}' => [
                'get' => [
                    'operationId' => 'getCardsByBlog',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Obtener cards de un blog',
                    'description' => 'Obtiene todas las cards asociadas a un blog específico.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => false, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Lista de cards del blog'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Blog no encontrado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/cards' => [
                'get' => [
                    'operationId' => 'listCards',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Listar todas las cards',
                    'description' => 'Obtiene la lista de todas las cards del sistema.',
                    'security' => [['sanctum' => []]],
                    'responses' => [
                        '200' => ['description' => 'Lista de cards'],
                        '401' => ['description' => 'No autenticado'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/card/blog/image_head/{id}' => [
                'post' => [
                    'operationId' => 'uploadHeaderImage',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Subir imagen de encabezado',
                    'description' => 'Sube una imagen para el encabezado de un blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['type' => 'object']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Imagen subida exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '400' => ['description' => 'Error en la carga'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/card/blog/images_body/{id}' => [
                'post' => [
                    'operationId' => 'uploadBodyImages',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Subir imágenes del cuerpo',
                    'description' => 'Sube imágenes para el cuerpo del blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['type' => 'object']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Imágenes subidas exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '400' => ['description' => 'Error en la carga'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/card/blog/images_footer/{id}' => [
                'post' => [
                    'operationId' => 'uploadFooterImages',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Subir imágenes del pie de página',
                    'description' => 'Sube imágenes para el pie de página del blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content' => ['application/json' => ['schema' => ['type' => 'object']]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Imágenes subidas exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '400' => ['description' => 'Error en la carga'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
            ],
            '/delete_carpet/{id}' => [
                'delete' => [
                    'operationId' => 'deleteCarperImages',
                    'tags' => ['Blogs & Cards'],
                    'summary' => 'Eliminar carpeta de imágenes',
                    'description' => 'Elimina la carpeta y todas las imágenes de un blog.',
                    'security' => [['sanctum' => []]],
                    'parameters' => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Carpeta eliminada exitosamente'],
                        '401' => ['description' => 'No autenticado'],
                        '404' => ['description' => 'Carpeta no encontrada'],
                        '500' => ['description' => 'Error del servidor'],
                    ],
                ],
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
        $this->info('✅ Documentación de Blogs y Cards actualizada exitosamente');

        return 0;
    }
}
