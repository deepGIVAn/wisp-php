<?php

return [
    'connection' => env('DB_CONNECTION', 'sqlite'),
    'sqlite' => [
        'database' => base_path(env('DB_DATABASE', 'storage/database/app.sqlite')),
    ],
    'mysql' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_NAME', 'wisp'),

        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
];
