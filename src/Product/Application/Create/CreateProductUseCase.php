<?php

declare(strict_types=1);

namespace App\Product\Application\Create;

use App\Product\Application\Dto\ProductResponseDto;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Repository\ProductRepositoryInterface;
use App\Product\Domain\ValueObject\ProductName;
use App\Product\Domain\ValueObject\ProductPrice;

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
        $this->repository->flush();

        return ProductResponseDto::fromEntity($product);
    }
}
