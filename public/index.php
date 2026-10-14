<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Core\Request;
use Core\Router;

$router = new Router();
require BASE_PATH . '/routes/web.php';

$request = Request::capture();

try {
    $response = $router->dispatch($request);
    $response->send();
} catch (Throwable $e) {
    if (config('app.debug')) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo $e->getMessage() . "\n\n" . $e->getTraceAsString();
        exit;
    }

    http_response_code(500);
    echo 'Something went wrong.';
}
