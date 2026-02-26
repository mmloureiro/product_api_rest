<?php

declare(strict_types=1);

namespace Flat101\Product\Application\Dto;

use Flat101\Product\Domain\Entity\Product;

final readonly class ProductResponseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
        public string $createdAt
    ) {
    }

    public static function fromEntity(Product $product): self
    {
        return new self(
            (int) $product->getId(),
            $product->getName()->value(),
            $product->getPrice()->amount(),
            $product->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
    }
}
