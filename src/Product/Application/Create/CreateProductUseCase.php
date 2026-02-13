<?php

namespace Flat101\Product\Application\Create;

use Flat101\Product\Domain\Entity\Product;
use Flat101\Product\Domain\Repository\ProductRepositoryInterface;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }

    public function execute(string $name, float $price): Product
    {
        $product = new Product($name, $price);

        $this->repository->save($product);

        return $product;
    }

}
