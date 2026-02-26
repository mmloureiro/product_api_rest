<?php

declare(strict_types=1);

namespace Flat101\Product\Application\Find;

use Flat101\Product\Application\Dto\ProductResponseDto;
use Flat101\Product\Domain\Exception\ProductNotFoundException;
use Flat101\Product\Domain\Repository\ProductRepositoryInterface;

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
