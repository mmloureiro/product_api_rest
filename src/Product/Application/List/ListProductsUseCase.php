<?php

declare(strict_types=1);

namespace Flat101\Product\Application\List;

use Flat101\Product\Application\Dto\ProductResponseDto;
use Flat101\Product\Domain\Entity\Product;
use Flat101\Product\Domain\Repository\ProductRepositoryInterface;

class ListProductsUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {}

    /**
     * @return ProductResponseDto[]
     */
    public function execute(): array
    {
        $products = $this->repository->findAll();

        return array_map(
            fn(Product $product) => ProductResponseDto::fromEntity($product),
            $products
        );
    }
}
