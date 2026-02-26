<?php

declare(strict_types=1);

namespace App\Product\Application\Delete;

use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Domain\Repository\ProductRepositoryInterface;

class DeleteProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }

    public function execute(int $id): void
    {
        $product = $this->repository->find($id);

        if (!$product) {
            throw ProductNotFoundException::fromId($id);
        }

        $this->repository->remove($product);
        $this->repository->flush();
    }
}
