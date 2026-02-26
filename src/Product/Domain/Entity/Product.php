<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use App\Product\Domain\ValueObject\ProductName;
use App\Product\Domain\ValueObject\ProductPrice;

class Product
{
    private ?int $id = null;
    private string $name;
    private int $price;
    private DateTimeInterface $createdAt;

    public function __construct(ProductName $name, ProductPrice $price)
    {
        $this->name = $name->value();
        $this->price = $price->amountInCents();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ProductName
    {
        return new ProductName($this->name);
    }

    public function getPrice(): ProductPrice
    {
        // This is a bit tricky with internal primitive storage for Doctrine, 
        // but we return the Value Object to maintain domain integrity.
        return new ProductPrice($this->price / 100);
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function update(ProductName $name, ProductPrice $price): void
    {
        $this->name = $name->value();
        $this->price = $price->amountInCents();
    }
}
