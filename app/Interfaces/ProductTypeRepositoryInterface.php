<?php

namespace App\Interfaces;

interface ProductTypeRepositoryInterface
{
    public function create(array $productType): array;
    public function pagination(int $perPage): array;
    public function find(int $id): array;
    public function all(): array;
}
