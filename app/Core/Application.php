<?php

namespace App\Core;

class Application
{
    private static Container $container;

    public static function resolve(Container $container)
    {
        self::$container = $container;
    }

    public static function make(string $key)
    {
        return self::$container->resolve($key);
    }

    public function setExeptionHandler(): void
    {
        set_exception_handler(fn ($exception) => $this->exceptionHandler($exception));
    }

    public function exceptionHandler($exception)
    {
        response()->json([
            'message' => $exception->getMessage(),
        ], 500);

        exit;
    }
}
