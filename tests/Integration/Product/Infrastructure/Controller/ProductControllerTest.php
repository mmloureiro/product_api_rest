<?php

namespace App\Tests\Integration\Product\Infrastructure\Controller;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\ValueObject\ProductName;
use App\Product\Domain\ValueObject\ProductPrice;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client        = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
        parent::tearDown();
    }

    private function cleanDatabase(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Product\Domain\Entity\Product')->execute();
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
        $product1 = new Product(new ProductName('Test Product 1'), new ProductPrice(99.99));
        $product2 = new Product(new ProductName('Test Product 2'), new ProductPrice(149.99));

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
        $product = new Product(new ProductName('Structured Product'), new ProductPrice(79.99));

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
        $this->assertIsNumeric($data[0]['price']);
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
                'name'  => 'Smartphone XYZ',
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
        $this->assertEquals(499.99, $product->getPrice()->amount());
    }

    public function testCreateProductReturns400WithInvalidData(): void
    {
        $this->client->catchExceptions(true);

        // Enviamos un nombre demasiado corto y un precio válido
        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                'name'  => 'Ab',
                'price' => -10.0
            ])
        );

        $response = $this->client->getResponse();

        // MapRequestPayload suele devolver 422 Unprocessable Entity
        $this->assertEquals(422, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertIsArray($data, 'La respuesta no es un JSON válido');
        $this->assertArrayHasKey('violations', $data);
    }

    public function testGetProductByIdReturns200(): void
    {
        // 1. Creamos un producto directamente con Doctrine
        $product = new Product(new ProductName('Find Me'), new ProductPrice(50.0));
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // 2. Intentamos recuperarlo vía API
        $this->client->request('GET', '/api/products/' . $product->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Find Me', $data['name']);
    }

    public function testGetProductByIdReturns404IfNotFound(): void
    {
        $this->client->request('GET', '/api/products/9999');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteProductSuccessfully(): void
    {
        // Crear producto
        $this->client->request('POST', '/api/products', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'Elimíname', 'price' => 5.0])
        );
        $id = json_decode($this->client->getResponse()->getContent(), true)['id'];

        // Borrar producto
        $this->client->request('DELETE', "/api/products/$id");
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());

        // Verificar que ya no existe
        $this->client->request('GET', "/api/products/$id");
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
