<?php

namespace App\Core;

use InvalidArgumentException;

class FormRequest
{
    private array $request = [];

    private array $errors = [];

    private array $validated = [];

    private array $messages = [
        'required' => 'The :attribute field is required',
        'integer'  => 'The :attribute field must be an integer',
        'decimal'  => 'The :attribute field must be a decimal',
        'string'   => 'The :attribute field must be a string',
    ];

    public function __construct()
    {
        $this->parseRequest();
    }

    public function all(): array
    {
        return $this->request;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->request[$key] ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function validate(array $rules): void
    {
        foreach ($rules as $key => $rule) {
            $this->validateRule($key, $rule);

            if(!array_key_exists($key, $this->errors)) {
                $this->validated[$key] = $this->request[$key];
            }
        }

        if (!empty($this->errors)) {
            $this->abortRequest();
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->validated;
    }

    private function parseRequest(): void
    {
        $input = file_get_contents('php://input');

        if ($input) {
            $this->request = json_decode($input, true);
        }
    }

    private function validateRule(string $key, string $rule): void
    {
        $value     = $this->get($key);
        $ruleParts = explode('|', $rule);

        foreach ($ruleParts as $rulePart) {
            $this->validateRulePart($key, $rulePart, $value);
        }
    }

    private function validateRulePart(string $key, string $rule): void
    {
        match ($rule) {
            'required' => $this->validateRequired($key),
            'integer'  => $this->validateInteger($key),
            'decimal'  => $this->validateDecimal($key),
            'string'   => $this->validateString($key),
            'array'    => $this->validateArray($key),
            default    => throw new InvalidArgumentException("Rule {$rule} is not supported")
        };
    }

    private function validateArray(string $key): void
    {
        if (!is_array($this->request[$key])) {
            $this->errors[$key][] = $this->resolveMessage($this->messages['array'], $key);
        }
    }

    private function validateRequired(string $key): void
    {
        if (!isset($this->request[$key]) || is_null($this->request[$key])) {
            $this->errors[$key][] = $this->resolveMessage($this->messages['required'], $key);
        }
    }

    private function validateInteger(string $key): void
    {
        if (is_numeric($this->request[$key])) {
            $this->request[$key] = (int)$this->request[$key];
        }

        if (!is_int($this->request[$key])) {
            $this->errors[$key][] = $this->resolveMessage($this->messages['integer'], $key);
        }
    }

    private function validateDecimal(string $key): void
    {
        if (is_int($this->request[$key])) {
            $this->request[$key] = (float)$this->request[$key];
        }

        if (!is_float($this->request[$key])) {
            $this->errors[$key][] = $this->resolveMessage($this->messages['decimal'], $key);
        }
    }

    private function validateString(string $key): void
    {
        if (!is_string($this->request[$key])) {
            $this->errors[$key][] = $this->resolveMessage($this->messages['string'], $key);
        }
    }

    private function abortRequest(): void
    {
        response()->json([
            'message' => 'The given data was invalid.',
            'errors'  => $this->errors,
        ], 422);

        exit;
    }

    private function resolveMessage(string $message, string $key): string
    {
        return str_replace(':attribute', $key, $message);
    }
}
