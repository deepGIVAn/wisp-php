<?php

namespace Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        return View::make($view, $data, $layout);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }
}
