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
        $this->setName($name);
        $this->setPrice($price);
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

    private function setName(ProductName $name): void
    {
        $this->name = $name->value();
    }

    public function getPrice(): ProductPrice
    {
        return new ProductPrice($this->price / 100);
    }

    private function setPrice(ProductPrice $price): void
    {
        $this->price = $price->amountInCents();
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function update(ProductName $name, ProductPrice $price): void
    {
        $this->setName($name);
        $this->setPrice($price);
    }
}
