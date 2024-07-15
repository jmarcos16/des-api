<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function find(int $id): array;
    public function create(array $product): array;
    public function pagination(int $perPage): array;
    public function all(): array;
}
