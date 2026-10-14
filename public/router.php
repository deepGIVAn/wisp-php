<?php

/**
 * Router for PHP built-in server:
 * php -S localhost:8000 -t public public/router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$file = __DIR__ . $uri;

if ($uri !== '/' && is_file($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $cacheable = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'otf', 'eot'];

    if (in_array($ext, $cacheable, true)) {
        header('Cache-Control: public, max-age=2592000, immutable');
    }

    return false;
}

require __DIR__ . '/index.php';
