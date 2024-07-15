<?php

namespace App\Core;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class Container
{
    protected array $bindings = [];

    public function bind(string $abstract, $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?? $abstract;
    }

    public function resolve(string $abstract)
    {
        $concrete = $this->bindings[$abstract] ?? $abstract;

        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new ReflectionException("Class {$concrete} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            return new $concrete();
        }

        return $reflector->newInstanceArgs(
            $this->parameters($constructor, $this)
        );
    }

    public function parameters($method, Container $container)
    {
        $parameters = [];

        foreach ($method->getParameters() as $param) {
            $dependency = $param->getType();

            if ($dependency instanceof ReflectionNamedType && !$dependency->isBuiltin()) {
                $parameters[] = $container->resolve($dependency->getName());
            }
        }

        return $parameters;
    }
}
