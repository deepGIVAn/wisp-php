<?php

namespace Core;

class Autoloader
{
    /** @var array<string, string> */
    private static array $prefixes = [];

    public static function register(array $prefixes): void
    {
        self::$prefixes = $prefixes;
        spl_autoload_register([self::class, 'load']);
    }

    public static function load(string $class): void
    {
        foreach (self::$prefixes as $prefix => $baseDir) {
            if (!str_starts_with($class, $prefix)) {
                continue;
            }

            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

            if (is_file($file)) {
                require $file;
                return;
            }
        }
    }
}
