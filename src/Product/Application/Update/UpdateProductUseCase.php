<?php

declare(strict_types=1);

namespace App\Product\Application\Update;

use App\Product\Application\Dto\ProductResponseDto;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Domain\Repository\ProductRepositoryInterface;
use App\Product\Domain\ValueObject\ProductName;
use App\Product\Domain\ValueObject\ProductPrice;

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
        $this->repository->flush();

        return ProductResponseDto::fromEntity($product);
    }
}
