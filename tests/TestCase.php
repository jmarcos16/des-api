<?php

namespace Tests;

use App\Database\DatabaseConnection;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $mockDatabaseConnection;

    protected $mockPDO;

    protected $mockStatement;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockDatabaseConnection = $this->createMock(DatabaseConnection::class);
        $this->mockPDO                = $this->createMock(\PDO::class);
        $this->mockStatement          = $this->createMock(\PDOStatement::class);

        $this->mockDatabaseConnection->method('getConnection')
            ->willReturn($this->mockPDO);
    }

    public function tearDown(): void
    {
        $this->mockDatabaseConnection = null;
        $this->mockPDO                = null;
        $this->mockStatement          = null;
    }
}
