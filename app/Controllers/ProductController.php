<?php

namespace App\Controllers;

use App\Core\FormRequest;
use App\Interfaces\{ProductRepositoryInterface, ProductTypeRepositoryInterface};
use App\Utils\JsonResponse;

class ProductController
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ProductTypeRepositoryInterface $productType
    ) {
    }

    public function index(): JsonResponse
    {
        $products = $this->productRepository->pagination(10);

        return response()->ok($products);
    }

    public function store(FormRequest $request): JsonResponse
    {
        try {
            $request->validate([
                'name'            => 'required',
                'price'           => 'required|string',
                'product_type_id' => 'required|integer',
            ]);

            $this->productType->find($request->get('product_type_id'));
            $product = $this->productRepository->create([
                'name'            => $request->get('name'),
                'price'           => convertToDecimal($request->get('price')),
                'product_type_id' => $request->get('product_type_id'),

            ]);

            return response()->created($product);
        } catch (\Exception $e) {
            return response()->badRequest($e->getMessage());
        }
    }

    public function findAll(): JsonResponse
    {
        $products = $this->productRepository->all();

        return response()->ok($products);
    }
}
