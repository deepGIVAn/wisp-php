<?php

namespace Core;

class Component
{
    public static function render(string $name, array $props = []): string
    {
        $path = base_path('app/Views/components/' . str_replace('.', '/', $name) . '.php');

        if (!is_file($path)) {
            throw new \RuntimeException("Component [{$name}] not found.");
        }

        extract($props, EXTR_SKIP);
        ob_start();
        include $path;
        return (string) ob_get_clean();
    }
}
