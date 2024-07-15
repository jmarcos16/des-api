<?php

namespace App\Controllers;

use App\Core\FormRequest;
use App\Interfaces\ProductTypeRepositoryInterface;
use App\Utils\JsonResponse;
use Exception;

class ProductTypeController
{
    public function __construct(
        protected ProductTypeRepositoryInterface $productType
    ) {
    }

    public function index(): JsonResponse
    {
        $productTypes = $this->productType->pagination(10);

        return response()->ok($productTypes);
    }

    public function store(FormRequest $request): JsonResponse
    {
        try {

            $request->validate([
                'name'        => 'required|string',
                'tax_percent' => 'required|integer',
            ]);

            $productType = $this->productType->create($request->validated());

            return response()->created($productType);
        } catch (Exception $e) {
            return response()->badRequest($e->getMessage());
        }
    }

    public function findAll(): JsonResponse
    {
        $productTypes = $this->productType->all();

        return response()->ok($productTypes);
    }
}
