<?php

declare(strict_types=1);

namespace Flat101\Product\Application\Create;

use Flat101\Product\Application\Dto\ProductResponseDto;
use Flat101\Product\Domain\Entity\Product;
use Flat101\Product\Domain\Repository\ProductRepositoryInterface;
use Flat101\Product\Domain\ValueObject\ProductName;
use Flat101\Product\Domain\ValueObject\ProductPrice;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }

    public function execute(string $name, float $price): ProductResponseDto
    {
        $product = new Product(
            new ProductName($name),
            new ProductPrice($price)
        );

        $this->repository->save($product);

        return ProductResponseDto::fromEntity($product);
    }
}
