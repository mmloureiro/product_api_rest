<?php

declare(strict_types=1);

namespace App\Product\Application\Delete;

use App\Product\Domain\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            throw new NotFoundHttpException("Product with ID $id not found");
        }

        $this->repository->remove($product);
    }
}
