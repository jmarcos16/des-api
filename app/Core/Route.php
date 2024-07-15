<?php

namespace App\Core;

class Route
{
    protected array $routes = [];

    private function addRoute(string $method, string $path, mixed $callback): void
    {
        $controller = is_array($callback) ? $callback[0] : $callback;
        $methodName = is_array($callback) ? $callback[1] : null;

        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'controller' => $controller,
            'action'     => $methodName,
        ];
    }

    public function get(string $path, mixed $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, mixed $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function put(string $path, mixed $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    public function delete(string $path, mixed $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
