<?php

use App\Core\FormRequest;
use App\Utils\JsonResponse;

if (!function_exists('response')) {
    /**
     * Return a new instance of Reponse
     */
    function response(): JsonResponse
    {
        return new JsonResponse();
    }

}

if (!function_exists('abort')) {
    /**
     * Return a new instance of JsonResponse with a 404 status code
     */
    function abort(string $message): JsonResponse
    {
        return response()->notFound($message);
    }
}

if (!function_exists('request')) {
    /**
     * Return a new instance of Request
     */
    function request(): FormRequest
    {
        return new FormRequest();
    }
}

if (!function_exists('env')) {
    /**
     * Return the value of an environment variable
     */
    function env(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('convertToDecimal')) {
    /**
     * Convert a string to a decimal
     */
    function convertToDecimal(string $value): float
    {
        $value = str_replace('R$', '', $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}
