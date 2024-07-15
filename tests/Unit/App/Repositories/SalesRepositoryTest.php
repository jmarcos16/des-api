<?php

namespace Tests\Unit\App\Repositories;

use App\Repositories\Postgre\SaleRepository;
use Tests\TestCase;

class SalesRepositoryTest extends TestCase
{
    protected SaleRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new SaleRepository($this->mockDatabaseConnection);
    }

    public function testCreateSale(): void
    {
        $saleData = [
            'items' => [
                ['productId' => 1, 'quantity' => 2, 'total' => 100, 'taxes' => 10],
                ['productId' => 2, 'quantity' => 1, 'total' => 50, 'taxes' => 5],
            ],
        ];

        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->exactly(3))->method('execute');

        $this->mockPDO->method('lastInsertId')->willReturn('1');

        $result = $this->repository->create($saleData);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testPagination(): void
    {
        $items = [
            ['id' => 1, 'total' => 100, 'total_tax' => 10],
            ['id' => 2, 'total' => 50, 'total_tax' => 5],
        ];

        $this->mockPDO->method('query')->willReturn($this->mockStatement);
        $this->mockStatement->method('fetchColumn')->willReturn(10);
        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->expects($this->once())->method('execute');
        $this->mockStatement->method('fetchAll')->willReturn($items);

        $result = $this->repository->paginate(2);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertCount(2, $result['data']);
        $this->assertEquals(10, $result['meta']['total']);
        $this->assertEquals(2, $result['meta']['per_page']);
        $this->assertEquals(1, $result['meta']['page']);
        $this->assertTrue($result['meta']['has_more']);
        $this->assertFalse($result['meta']['has_previous']);
    }

    public function testCreateSaleItems(): void
    {
        $items = [
            ['productId' => 1, 'quantity' => 2, 'total' => 100, 'taxes' => 10],
            ['productId' => 2, 'quantity' => 1, 'total' => 50, 'taxes' => 5],
        ];

        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->exactly(2))->method('execute');

        $this->repository->createSaleItems($items, 1);

    }
}