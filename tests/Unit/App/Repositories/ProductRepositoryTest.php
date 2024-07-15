<?php

namespace Tests\Unit\App\Repositories;

use App\Repositories\Postgre\ProductRepository;
use Tests\TestCase;

final class ProductRepositoryTest extends TestCase
{
    protected ProductRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository($this->mockDatabaseConnection);
    }

    public function testCreateProduct(): void
    {
        $productData = [
            'name'            => 'Test Product',
            'price'           => 100,
            'product_type_id' => 1,
        ];

        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->expects($this->once())->method('execute')->with($this->equalTo([
            'name'            => $productData['name'],
            'price'           => $productData['price'],
            'product_type_id' => 1,
        ]));

        $this->mockPDO->method('lastInsertId')->willReturn('1');
        $result = $this->repository->create($productData);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testPagination(): void
    {

        $items = [
            ['id' => 1, 'name' => 'Test Product 1', 'price' => 100, 'product_type' => 'Type 1'],
            ['id' => 2, 'name' => 'Test Product 2', 'price' => 200, 'product_type' => 'Type 2'],
        ];

        $this->mockPDO->method('query')->willReturn($this->mockStatement);
        $this->mockStatement->method('fetchColumn')->willReturn(10);
        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->expects($this->once())->method('execute');
        $this->mockStatement->method('fetchAll')->willReturn($items);

        $result = $this->repository->pagination(2);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertCount(2, $result['data']);
        $this->assertEquals(10, $result['meta']['total']);
        $this->assertEquals(2, $result['meta']['per_page']);
        $this->assertEquals(1, $result['meta']['page']);
        $this->assertTrue($result['meta']['has_more']);
        $this->assertFalse($result['meta']['has_previous']);
        $this->assertEquals(5, $result['meta']['total_pages']);
    }

    public function testFindProduct(): void
    {
        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->expects($this->once())->method('execute')->with($this->equalTo(['id' => 1]));
        $this->mockStatement->method('fetch')->willReturn([
            'id'              => 1,
            'name'            => 'Test Product',
            'price'           => 100,
            'product_type_id' => 1,
        ]);

        $result = $this->repository->find(1);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Product', $result['name']);
        $this->assertEquals(100, $result['price']);
        $this->assertEquals(1, $result['product_type_id']);
    }

    public function testFindProductNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product not found');

        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->expects($this->once())->method('execute')->with($this->equalTo(['id' => 1]));
        $this->mockStatement->method('fetch')->willReturn(false);

        $this->repository->find(1);
    }

    public function testFindAllProducts(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Test Product 1', 'price' => 100, 'product_type' => 'Type 1'],
            ['id' => 2, 'name' => 'Test Product 2', 'price' => 200, 'product_type' => 'Type 2'],
        ];

        $this->mockPDO->method('query')->willReturn($this->mockStatement);
        $this->mockStatement->method('fetchAll')->willReturn($items);

        $result = $this->repository->all();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('Test Product 1', $result[0]['name']);
        $this->assertEquals(100, $result[0]['price']);
        $this->assertEquals('Type 1', $result[0]['product_type']);
        $this->assertEquals(2, $result[1]['id']);
        $this->assertEquals('Test Product 2', $result[1]['name']);
        $this->assertEquals(200, $result[1]['price']);
        $this->assertEquals('Type 2', $result[1]['product_type']);
    }
}
