<?php

namespace Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $content = self::renderFile($view, $data);

        if ($layout === null) {
            return $content;
        }

        return self::renderFile($layout, array_merge($data, ['content' => $content]));
    }

    public static function make(string $view, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        return Response::make(self::render($view, $data, $layout));
    }

    private static function renderFile(string $view, array $data): string
    {
        $path = base_path('app/Views/' . str_replace('.', '/', $view) . '.php');

        if (!is_file($path)) {
            throw new \RuntimeException("View [{$view}] not found at {$path}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $path;
        return (string) ob_get_clean();
    }
}
