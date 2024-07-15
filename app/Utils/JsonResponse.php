<?php

namespace App\Utils;

class JsonResponse
{
    public const STATUS_OK = 200;

    public const STATUS_CREATED = 201;

    public const STATUS_NO_CONTENT = 204;

    public const STATUS_BAD_REQUEST = 400;

    public const STATUS_NOT_FOUND = 404;

    public const STATUS_INTERNAL_SERVER_ERROR = 500;

    public static function json(array $data, int $status = 200): self
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        http_response_code($status);
        echo json_encode($data);

        return new self();
    }

    public static function ok(array $data): self
    {
        return self::json($data, self::STATUS_OK);
    }

    public static function created(array $data): self
    {
        return self::json($data, self::STATUS_CREATED);
    }

    public static function notFound(string $message): self
    {
        return self::json(['message' => $message], self::STATUS_NOT_FOUND);
    }

    public function badRequest(string $message): self
    {
        return self::json(['message' => $message], self::STATUS_BAD_REQUEST);
    }

    public function noContent(): self
    {
        return self::json([], self::STATUS_NO_CONTENT);
    }

    public static function resolveOptions(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);

            exit();
        }
    }
}
