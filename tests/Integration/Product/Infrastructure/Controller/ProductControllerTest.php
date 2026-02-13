<?php

namespace Flat101\Tests\Integration\Product\Infrastructure\Controller;

use Flat101\Product\Domain\Entity\Product;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
        parent::tearDown();
    }

    private function cleanDatabase(): void
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        foreach ($products as $product) {
            $this->entityManager->remove($product);
        }
        $this->entityManager->flush();
    }

    public function testGetProductsReturns200(): void
    {
        $this->client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetProductsReturnsEmptyArrayWhenNoProducts(): void
    {
        $this->client->request('GET', '/api/products');

        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    public function testGetProductsReturnsProductsList(): void
    {
        // Crear productos de prueba
        $product1 = new Product('Test Product 1', 99.99);
        $product2 = new Product('Test Product 2', 149.99);

        $this->entityManager->persist($product1);
        $this->entityManager->persist($product2);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/products');

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(2, $data);
        $this->assertEquals('Test Product 1', $data[0]['name']);
        $this->assertEquals(99.99, $data[0]['price']);
        $this->assertEquals('Test Product 2', $data[1]['name']);
        $this->assertEquals(149.99, $data[1]['price']);
    }

    public function testGetProductsReturnsCorrectStructure(): void
    {
        $product = new Product('Structured Product', 79.99);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/products');

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
        $this->assertArrayHasKey('price', $data[0]);
        $this->assertArrayHasKey('createdAt', $data[0]);

        $this->assertIsInt($data[0]['id']);
        $this->assertIsString($data[0]['name']);
        $this->assertIsFloat($data[0]['price']);
        $this->assertIsString($data[0]['createdAt']);

        // Verificar que createdAt es una fecha válida
        $this->assertNotFalse(DateTime::createFromFormat(DateTimeInterface::ATOM, $data[0]['createdAt']));
    }

    public function testCreateProductSuccessfully(): void
    {
        // 1. Petición POST con datos válidos
        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Smartphone XYZ',
                'price' => 499.99
            ])
        );

        // 2. Verificamos respuesta de la API
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Smartphone XYZ', $data['name']);
        $this->assertEquals(499.99, $data['price']);

        // 3. Verificamos persistencia
        $product = $this->entityManager->getRepository(Product::class)
                                       ->findOneBy(['name' => 'Smartphone XYZ']);

        $this->assertNotNull($product);
        $this->assertEquals(499.99, $product->getPrice());
    }

    public function testCreateProductReturns400WithInvalidData(): void
    {
        // Enviamos un nombre demasiado corto y un precio válido
        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Ab',
                'price' => 10.0
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('El nombre debe tener al menos 3 caracteres', $data['error']);
    }
}

