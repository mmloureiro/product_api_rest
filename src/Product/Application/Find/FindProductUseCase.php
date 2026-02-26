<?php

declare(strict_types=1);

namespace App\Product\Application\Find;

use App\Product\Application\Dto\ProductResponseDto;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Domain\Repository\ProductRepositoryInterface;

class FindProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }

    public function execute(int $id): ProductResponseDto
    {
        $product = $this->repository->find($id);

        if (!$product) {
            throw ProductNotFoundException::fromId($id);
        }

        return ProductResponseDto::fromEntity($product);
    }
}
