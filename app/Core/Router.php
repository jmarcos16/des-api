<?php

namespace App\Core;

use App\Core\Exceptions\RouterException;
use ReflectionMethod;

class Router
{
    public function __construct(
        private Container $container
    ) {
    }

    public function resolve($routes): void
    {
        $requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';

        foreach ($routes as $route) {
            if ($route['method'] === $_SERVER['REQUEST_METHOD'] && $this->matchRoute($route['path'], $requestUri, $params)) {
                $this->makeInstance($route['controller'], $route['action'], $params);

                return;
            }
        }

        throw new RouterException('Route not found', 404);
    }

    private function makeInstance($controller, $action, $params)
    {
        if (class_exists($controller)) {
            $controller = $this->container->resolve($controller);

            if (method_exists($controller, $action)) {
                $resolve = $this->container->parameters(
                    (new ReflectionMethod($controller, $action)),
                    $this->container
                );
                $resolve = array_merge($resolve, $params);

                return call_user_func_array([$controller, $action], $resolve);
            }

            throw new RouterException('Method not found', 404);
        }

        throw new RouterException('Controller not found', 404);
    }

    private function matchRoute(string $routePath, string $requestUri, &$params): bool
    {
        $routePath = preg_replace('/{(\w+)}/', '(\w+)', $routePath);
        $regex     = "#^$routePath$#";

        if (preg_match($regex, $requestUri, $matches)) {
            array_shift($matches);
            $params = $matches;

            return true;
        }

        return false;
    }
}
