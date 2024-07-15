<?php

namespace Tests\Unit\App\Repositories;

use App\Repositories\Postgre\ProductTypeRepository;
use Tests\TestCase;

class ProductTypeRepositoryTest extends TestCase
{
    protected ProductTypeRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductTypeRepository($this->mockDatabaseConnection);
    }

    public function testCreateProductType(): void
    {
        $productTypeData = [
            'name'        => 'Test Product Type',
            'tax_percent' => 10,
        ];

        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->expects($this->once())->method('execute')->with($this->equalTo($productTypeData));

        $this->mockPDO->method('lastInsertId')->willReturn('1');
        $result = $this->repository->create($productTypeData);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testPagination(): void
    {

        $items = [
            ['id' => 1, 'name' => 'Test Product Type 1', 'tax_percent' => 10],
            ['id' => 2, 'name' => 'Test Product Type 2', 'tax_percent' => 10],
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
    }

    public function testFindProductType(): void
    {
        $productTypeData = [
            'id'          => 1,
            'name'        => 'Test Product Type',
            'tax_percent' => 10,
        ];

        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->expects($this->once())->method('execute')->with($this->equalTo(['id' => 1]));
        $this->mockStatement->method('fetch')->willReturn($productTypeData);

        $result = $this->repository->find(1);

        $this->assertIsArray($result);
        $this->assertEquals($productTypeData, $result);
    }

    public function testFindProductTypeNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product type not found');
        $this->expectExceptionCode(404);

        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->expects($this->once())->method('execute')->with($this->equalTo(['id' => 1]));
        $this->mockStatement->method('fetch')->willReturn(false);

        $this->repository->find(1);
    }

    public function testFindAllProductTypes(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Test Product Type 1', 'tax_percent' => 10],
            ['id' => 2, 'name' => 'Test Product Type 2', 'tax_percent' => 10],
        ];

        $this->mockPDO->method('query')->willReturn($this->mockStatement);
        $this->mockStatement->method('fetchAll')->willReturn($items);

        $result = $this->repository->all();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }
}
