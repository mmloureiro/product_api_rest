<?php

declare(strict_types=1);

namespace Flat101\Product\Application\Update;

use Flat101\Product\Application\Dto\ProductResponseDto;
use Flat101\Product\Domain\Exception\ProductNotFoundException;
use Flat101\Product\Domain\Repository\ProductRepositoryInterface;
use Flat101\Product\Domain\ValueObject\ProductName;
use Flat101\Product\Domain\ValueObject\ProductPrice;

class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }

    public function execute(int $id, string $name, float $price): ProductResponseDto
    {
        $product = $this->repository->find($id);

        if (!$product) {
            throw ProductNotFoundException::fromId($id);
        }

        $product->update(
            new ProductName($name),
            new ProductPrice($price)
        );

        $this->repository->save($product);

        return ProductResponseDto::fromEntity($product);
    }
}
