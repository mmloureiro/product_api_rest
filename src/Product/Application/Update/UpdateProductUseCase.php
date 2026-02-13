<?php

declare(strict_types=1);

namespace Flat101\Product\Application\Update;

use Flat101\Product\Domain\Entity\Product;
use Flat101\Product\Domain\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }

    public function execute(int $id, string $name, float $price): Product
    {
        $product = $this->repository->find($id);

        if (!$product) {
            throw new NotFoundHttpException("Product with ID $id not found");
        }

        $product->update($name, $price);
        $this->repository->save($product);

        return $product;
    }
}
