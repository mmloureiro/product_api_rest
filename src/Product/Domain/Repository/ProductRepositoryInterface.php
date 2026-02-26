<?php

namespace App\Product\Domain\Repository;

use App\Product\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @return Product[]
     */
    public function findAll(): array;

    public function find(int $id): ?Product;

    public function save(Product $product): void;

    public function remove(Product $product): void;

    public function flush(): void;
}
