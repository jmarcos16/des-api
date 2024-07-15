<?php

namespace App\Repositories\Postgre;

use App\Database\DatabaseConnection;
use App\Interfaces\ProductTypeRepositoryInterface;
use PDO;

class ProductTypeRepository implements ProductTypeRepositoryInterface
{
    protected string $table = 'product_types';

    protected PDO $connection;

    public function __construct(
        DatabaseConnection $db
    ) {
        $this->connection = $db->getConnection();
    }

    public function create(array $productType): array
    {
        $statement = $this->connection
            ->prepare('INSERT INTO product_types (name, tax_percent) VALUES (:name, :tax_percent)');
        $statement->execute([
            'name'        => $productType['name'],
            'tax_percent' => $productType['tax_percent'],
        ]);

        $productType['id'] = $this->connection->lastInsertId();

        return $productType;
    }

    public function pagination(int $perPage): array
    {
        $page        = request()->query('page', 1);
        $count       = $this->connection->query('SELECT COUNT(*) FROM product_types')->fetchColumn();
        $hasMore     = $count > ($page * $perPage);
        $hasPrevious = $page > 1;
        $tatalPages  = ceil($count / $perPage);

        $statement = $this->connection->prepare('SELECT * FROM product_types LIMIT :limit OFFSET :offset');
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);

        $statement->execute();

        return [
            'data' => $statement->fetchAll(PDO::FETCH_ASSOC),
            'meta' => [
                'total'        => $count,
                'per_page'     => $perPage,
                'page'         => $page,
                'has_more'     => $hasMore,
                'has_previous' => $hasPrevious,
                'total_pages'  => $tatalPages,
            ],
        ];
    }

    public function find(int $id): array
    {
        $statement = $this->connection->prepare('SELECT * FROM product_types WHERE id = :id');
        $statement->execute(['id' => $id]);

        $productType = $statement->fetch();

        if (!$productType) {
            throw new \Exception('Product type not found)', 404);
        }

        return $productType;
    }

    public function all(): array
    {
        $statement = $this->connection->query('SELECT * FROM product_types');

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
