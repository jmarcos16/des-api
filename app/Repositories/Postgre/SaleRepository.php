<?php

namespace App\Repositories\Postgre;

use App\Database\DatabaseConnection;
use App\Interfaces\SaleRepositoryInterface;
use PDO;

class SaleRepository implements SaleRepositoryInterface
{
    protected string $table = 'sales';

    protected PDO $connection;

    public function __construct(
        DatabaseConnection $db
    ) {
        $this->connection = $db->getConnection();
    }

    public function paginate(int $perPage): array
    {
        $page        = request()->query('page', 1);
        $count       = $this->connection->query('SELECT COUNT(*) FROM sales')->fetchColumn();
        $hasMore     = $count > ($page * $perPage);
        $hasPrevious = $page > 1;
        $totalPages  = ceil($count / $perPage);

        $statement = $this->connection
            ->prepare('SELECT sales.*, SUM(si.id) as items_count FROM sales LEFT JOIN sale_items si ON sales.id = si.sale_id GROUP BY sales.id ORDER BY sales.id DESC LIMIT :limit OFFSET :offset');

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
                'total_pages'  => $totalPages,
            ],
        ];
    }

    public function create(array $sale): array
    {

        $this->connection->beginTransaction();

        try {
            $statement = $this->connection
                ->prepare('INSERT INTO sales (total, total_tax) VALUES (:total, :total_tax)');

            $statement->execute([
                'total'     => $this->calculateTotal($sale['items']),
                'total_tax' => $this->calculateTotalTax($sale['items']),
            ]);

            $sale['id'] = $this->connection->lastInsertId();

            $this->createSaleItems($sale['items'], $sale['id']);
            $this->connection->commit();

            return $sale;
        } catch (\Throwable $th) {
            $this->connection->rollBack();

            throw $th;
        }
    }

    private function calculateTotal(array $items): float
    {
        return array_reduce($items, fn ($carry, $item) => $carry + ($item['total']), 0);
    }

    private function calculateTotalTax(array $items): float
    {
        return array_reduce($items, fn ($carry, $item) => $carry + ($item['taxes']), 0);
    }

    public function createSaleItems(array $items, int $saleId): void
    {
        $statement = $this->connection
            ->prepare('INSERT INTO sale_items (sale_id, product_id, quantity, price, tax) VALUES (:sale_id, :product_id, :quantity, :price, :tax)');

        foreach ($items as $item) {

            $statement->execute([
                'sale_id'    => $saleId,
                'product_id' => $item['productId'],
                'quantity'   => $item['quantity'],
                'price'      => $item['total'],
                'tax'        => $item['taxes'],
            ]);
        }
    }
}
