<?php

declare(strict_types=1);

namespace Flat101\Tests\Unit\Product\Domain\Entity;

use Flat101\Product\Domain\Entity\Product;
use Flat101\Product\Domain\ValueObject\ProductName;
use Flat101\Product\Domain\ValueObject\ProductPrice;
use DateTimeInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductCanBeCreated(): void
    {
        $name = new ProductName('Test Product');
        $price = new ProductPrice(99.99);
        $product = new Product($name, $price);

        $this->assertEquals('Test Product', $product->getName()->value());
        $this->assertEquals(99.99, $product->getPrice()->amount());
        $this->assertInstanceOf(DateTimeInterface::class, $product->getCreatedAt());
    }

    public function testPriceCannotBeNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price must be greater than 0');

        new ProductPrice(-10.50);
    }

    public function testPriceCannotBeZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price must be greater than 0');

        new ProductPrice(0);
    }

    public function testNameCannotBeShorterThan3Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name must be at least 3 characters long');

        new ProductName('AB');
    }

    public function testNameWithExactly3CharactersIsValid(): void
    {
        $name = new ProductName('ABC');
        $this->assertEquals('ABC', $name->value());
    }

    public function testPriceCanBePositive(): void
    {
        $price = new ProductPrice(0.01);
        $this->assertEquals(0.01, $price->amount());
    }

    public function testProductUpdate(): void
    {
        $product = new Product(new ProductName('Old Name'), new ProductPrice(10.0));
        
        $product->update(new ProductName('New Name'), new ProductPrice(20.0));

        $this->assertEquals('New Name', $product->getName()->value());
        $this->assertEquals(20.0, $product->getPrice()->amount());
    }
}
