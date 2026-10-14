<?php

namespace Core;

use Closure;

class Router
{
    /** @var array<int, array{methods: string[], uri: string, action: mixed}> */
    private array $routes = [];

    public function get(string $uri, mixed $action): self
    {
        return $this->add(['GET'], $uri, $action);
    }

    public function post(string $uri, mixed $action): self
    {
        return $this->add(['POST'], $uri, $action);
    }

    public function put(string $uri, mixed $action): self
    {
        return $this->add(['PUT'], $uri, $action);
    }

    public function delete(string $uri, mixed $action): self
    {
        return $this->add(['DELETE'], $uri, $action);
    }

    public function match(array $methods, string $uri, mixed $action): self
    {
        return $this->add(array_map('strtoupper', $methods), $uri, $action);
    }

    public function any(string $uri, mixed $action): self
    {
        return $this->add(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $uri, $action);
    }

    private function add(array $methods, string $uri, mixed $action): self
    {
        $uri = '/' . trim($uri, '/');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/') ?: '/';
        }

        $this->routes[] = [
            'methods' => $methods,
            'uri' => $uri === '' ? '/' : $uri,
            'action' => $action,
        ];

        return $this;
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if (!in_array($request->method(), $route['methods'], true)) {
                continue;
            }

            $params = $this->matchUri($route['uri'], $request->path());
            if ($params === null) {
                continue;
            }

            return $this->run($route['action'], $request, $params);
        }

        return Response::make('404 Not Found', 404);
    }

    private function matchUri(string $routeUri, string $path): ?array
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $path, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    private function run(mixed $action, Request $request, array $params): Response
    {
        if ($action instanceof Closure) {
            $result = $action($request, ...array_values($params));
            return $this->toResponse($result);
        }

        if (is_string($action) && str_contains($action, '@')) {
            [$class, $method] = explode('@', $action, 2);
            if (!class_exists($class)) {
                throw new \RuntimeException("Controller [{$class}] not found.");
            }
            $controller = new $class();
            $result = $controller->{$method}($request, ...array_values($params));
            return $this->toResponse($result);
        }

        if (is_array($action) && count($action) === 2) {
            [$class, $method] = $action;
            $controller = is_object($class) ? $class : new $class();
            $result = $controller->{$method}($request, ...array_values($params));
            return $this->toResponse($result);
        }

        throw new \RuntimeException('Invalid route action.');
    }

    private function toResponse(mixed $result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result)) {
            return Response::json($result);
        }

        return Response::make((string) $result);
    }
}
