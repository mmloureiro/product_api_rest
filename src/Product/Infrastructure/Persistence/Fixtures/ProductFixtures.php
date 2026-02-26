<?php

namespace App\Product\Infrastructure\Persistence\Fixtures;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\ValueObject\ProductName;
use App\Product\Domain\ValueObject\ProductPrice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            ['name' => 'Laptop HP ProBook', 'price' => 899.99],
            ['name' => 'Mouse Logitech MX Master', 'price' => 79.99],
            ['name' => 'Teclado Mecánico Keychron', 'price' => 129.99],
            ['name' => 'Monitor Dell 27 pulgadas', 'price' => 299.99],
            ['name' => 'Webcam Logitech C920', 'price' => 89.99],
            ['name' => 'Auriculares Sony WH-1000XM4', 'price' => 349.99],
            ['name' => 'SSD Samsung 1TB', 'price' => 119.99],
            ['name' => 'Cable USB-C Anker', 'price' => 15.99],
            ['name' => 'Hub USB Belkin', 'price' => 45.99],
            ['name' => 'Soporte para Laptop', 'price' => 34.99],
        ];

        foreach ($products as $productData) {
            $product = new Product(
                new ProductName($productData['name']),
                new ProductPrice($productData['price'])
            );

            $manager->persist($product);
        }

        $manager->flush();
    }
}

