<?php

namespace Core;

class App
{
    private static array $config = [];

    public static function bootstrap(string $basePath): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', $basePath);
        }

        Env::load($basePath . DIRECTORY_SEPARATOR . '.env');


        self::$config['app'] = require $basePath . '/config/app.php';
        self::$config['database'] = require $basePath . '/config/database.php';
        self::$config['cache'] = require $basePath . '/config/cache.php';

        if (self::$config['app']['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
            ini_set('display_errors', '0');
        }

        date_default_timezone_set('UTC');
    }

    public static function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return self::$config;
        }

        $segments = explode('.', $key);
        $value = self::$config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
