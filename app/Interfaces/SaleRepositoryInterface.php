<?php

namespace App\Interfaces;

interface SaleRepositoryInterface
{
    public function create(array $sale): array;
    public function paginate(int $perPage): array;
}
