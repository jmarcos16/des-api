<?php

namespace App\Repositories\Postgre;

use App\Database\DatabaseConnection;
use App\Interfaces\ProductRepositoryInterface;
use PDO;

class ProductRepository implements ProductRepositoryInterface
{
    private PDO $connection;

    protected string $table = 'products';

    public function __construct(DatabaseConnection $databaseConnection)
    {
        $this->connection = $databaseConnection->getConnection();
    }

    public function create(array $product): array
    {

        $statement = $this->connection
            ->prepare('INSERT INTO products (name, price, product_type_id) VALUES (:name, :price, :product_type_id)');
        $statement->execute([
            'name'            => $product['name'],
            'price'           => $product['price'],
            'product_type_id' => $product['product_type_id'],
        ]);

        $product['id'] = $this->connection->lastInsertId();

        return $product;
    }

    public function pagination(int $perPage): array
    {

        $page        = request()->query('page', 1);
        $count       = $this->connection->query('SELECT COUNT(*) FROM products')->fetchColumn();
        $hasMore     = $count > ($page * $perPage);
        $hasPrevious = $page > 1;
        $totalPages  = ceil($count / $perPage);

        $statement = $this->connection->prepare('SELECT products.id, products.name, products.price, product_types.name as product_type
         FROM products LEFT JOIN product_types ON products.product_type_id = product_types.id LIMIT :limit OFFSET :offset');
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);

        $statement->execute();

        return [
            'data' => $statement->fetchAll(PDO::FETCH_ASSOC),
            'meta' => [
                'total'        => $count,
                'per_page'     => $perPage,
                'page'         => (int)$page,
                'has_more'     => $hasMore,
                'has_previous' => $hasPrevious,
                'total_pages'  => $totalPages,
            ],
        ];
    }

    public function find(int $id): array
    {
        $statement = $this->connection->prepare('SELECT * FROM products WHERE id = :id');
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        if (!$product) {
            throw new \Exception('Product not found');
        }

        return $product;
    }

    public function all(): array
    {
        $statement = $this->connection->query('SELECT products.id, products.name, price, tax_percent, product_types.name as product_type FROM products LEFT JOIN product_types ON products.product_type_id = product_types.id');

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
