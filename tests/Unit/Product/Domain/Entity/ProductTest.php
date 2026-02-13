<?php

namespace Flat101\Tests\Unit\Product\Domain\Entity;

use Flat101\Product\Domain\Entity\Product;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductCanBeCreated(): void
    {
        $product = new Product('Test Product', 99.99);

        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(99.99, $product->getPrice());
        $this->assertInstanceOf(DateTimeInterface::class, $product->getCreatedAt());
    }

    public function testCreatedAtIsSetAutomatically(): void
    {
        $product = new Product('Test Product', 99.99);

        $this->assertInstanceOf(DateTimeInterface::class, $product->getCreatedAt());
        $this->assertLessThanOrEqual(new DateTime(), $product->getCreatedAt());
    }

    public function testPriceCannotBeNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price must be greater than 0');

        $product = new Product('Test Product', 99.99);
        $product->setPrice(-10.50);
    }

    public function testPriceCannotBeZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price must be greater than 0');

        $product = new Product('Test Product', 99.99);
        $product->setPrice(0);
    }

    public function testNameCannotBeShorterThan3Characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name must be at least 3 characters long');

        $product = new Product('Test Product', 99.99);
        $product->setName('AB');
    }

    public function testNameWithExactly3CharactersIsValid(): void
    {
        $product = new Product('Test Product', 99.99);
        $product->setName('ABC');

        $this->assertEquals('ABC', $product->getName());
    }

    public function testPriceCanBePositive(): void
    {
        $product = new Product('Test Product', 99.99);
        $product->setPrice(0.01);

        $this->assertEquals(0.01, $product->getPrice());
    }

    public function testProductGettersAndSetters(): void
    {
        $name    = 'Test Product';
        $price   = 99.99;
        $product = new Product($name, $price);

        $product->setName($name);
        $product->setPrice($price);

        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());
        $this->assertNull($product->getId());
    }
}
