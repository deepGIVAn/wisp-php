<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);

$autoload = BASE_PATH . '/vendor/autoload.php';

if (is_file($autoload)) {
    require $autoload;
} else {
    // Fallback until first: php composer.phar install
    require BASE_PATH . '/core/Autoloader.php';
    Core\Autoloader::register([
        'Core\\' => BASE_PATH . '/core/',
        'App\\' => BASE_PATH . '/app/',
    ]);
    require BASE_PATH . '/app/Helpers/functions.php';
}

Core\App::bootstrap(BASE_PATH);
