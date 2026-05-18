<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Métodos HTTP permitidos (especificar en lugar de usar *)
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],

    // Orígenes CORS permitidos - cargados desde .env
    'allowed_origins' => [
        'http://localhost:3000',
        'https://digimedia-marketing.com',
        'https://www.digimedia-marketing.com',
        'https://back.staging.digimedia-marketing.com',
    ],

    'allowed_origins_patterns' => [],

    // Headers permitidos en requests CORS
    'allowed_headers' => [
        'Content-Type',
        'Accept',
        'Authorization',
        'X-CSRF-Token',
        'X-API-Key',
        'X-Requested-With',
    ],

    'exposed_headers' => [
        'X-Total-Count',
        'X-Page-Count',
        'X-Links',
    ],

    // Cache CORS headers por 1 hora
    'max_age' => 3600,

    // Permitir credenciales (cookies, headers auth)
    'supports_credentials' => true,

];
