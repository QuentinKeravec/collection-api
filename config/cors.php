<?php

// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // mets l’URL de ton client React (Vite)
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],

    // si tu veux autoriser plusieurs origines en dev :
    // 'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Laisse false si tu utilises des Bearer tokens (Sanctum tokens / JWT)
    'supports_credentials' => true,
];
