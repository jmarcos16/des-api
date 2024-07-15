<?php

namespace App\Controllers;

use App\Core\FormRequest;
use App\Repositories\Postgre\SaleRepository;
use App\Utils\JsonResponse;

class SaleController
{
    public function __construct(
        protected SaleRepository $saleRepository
    ) {
    }

    public function index(): JsonResponse
    {
        $sales = $this->saleRepository->paginate(10);

        return response()->ok($sales);
    }

    public function create(FormRequest $request): JsonResponse
    {
        try {
            $request->validate([
                'items' => 'required|array',
            ]);

            $sale = $this->saleRepository->create($request->validated());

            return response()->created($sale);
        } catch (\Throwable $th) {
            return response()->badRequest('Error creating sale');
        }
    }
}
