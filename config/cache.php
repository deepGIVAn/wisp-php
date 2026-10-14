<?php

return [
    'driver' => env('CACHE_DRIVER', 'file'),
    'ttl' => (int) env('CACHE_TTL', 3600),
    'path' => base_path('storage/cache'),
];
