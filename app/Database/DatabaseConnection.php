<?php

namespace App\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
    protected PDO $connection;

    public function __construct()
    {
        $this->connection = $this->getConnection();
    }

    public function getConnection(): PDO
    {
        try {
            $this->connection = new PDO(
                env('DB_CONNECTION') . ':host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
                env('DB_USERNAME'),
                env('DB_PASSWORD')
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connection;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}
