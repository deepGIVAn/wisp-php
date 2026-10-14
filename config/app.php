<?php

return [
    'name' => env('APP_NAME', 'Wisp'),

    'env' => env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/'),
    'asset_version' => env('ASSET_VERSION', '1'),
];
