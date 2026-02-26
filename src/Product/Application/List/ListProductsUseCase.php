<?php

declare(strict_types=1);

namespace App\Product\Application\List;

use App\Product\Application\Dto\ProductResponseDto;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Repository\ProductRepositoryInterface;

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
