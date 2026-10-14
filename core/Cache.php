<?php

namespace Core;

class Cache
{
    public static function get(string $key, mixed $default = null): mixed
    {
        if (config('app.debug')) {
            // In debug mode prefer fresh data; still allow explicit cache use via remember below
        }

        $file = self::path($key);
        if (!is_file($file)) {
            return $default;
        }

        $payload = @unserialize((string) file_get_contents($file));
        if (!is_array($payload) || !array_key_exists('expires_at', $payload) || !array_key_exists('value', $payload)) {
            @unlink($file);
            return $default;
        }

        if ($payload['expires_at'] !== 0 && $payload['expires_at'] < time()) {
            @unlink($file);
            return $default;
        }

        return $payload['value'];
    }

    public static function put(string $key, mixed $value, ?int $ttl = null): void
    {
        $ttl ??= (int) config('cache.ttl', 3600);
        $expiresAt = $ttl > 0 ? time() + $ttl : 0;

        $dir = config('cache.path');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents(
            self::path($key),
            serialize(['expires_at' => $expiresAt, 'value' => $value]),
            LOCK_EX
        );
    }

    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        if (config('app.debug')) {
            return $callback();
        }

        $cached = self::get($key);
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        self::put($key, $value, $ttl);
        return $value;
    }

    public static function forget(string $key): void
    {
        $file = self::path($key);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    public static function flush(): void
    {
        $dir = config('cache.path');
        if (!is_dir($dir)) {
            return;
        }

        foreach (glob($dir . '/*.cache') ?: [] as $file) {
            @unlink($file);
        }
    }

    private static function path(string $key): string
    {
        return rtrim(config('cache.path'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . sha1($key)
            . '.cache';
    }
}
