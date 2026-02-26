<?php

declare(strict_types=1);

namespace App\Tests\Integration\Product\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UpdateProductControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testUpdateProductSuccessfully(): void
    {
        // creamos un producto para luego actualizarlo
        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name'  => 'Nombre original',
                'price' => 19.99
            ])
        );

        $createdProduct = json_decode($this->client->getResponse()->getContent(), true);
        $id             = $createdProduct['id'];

        // actualizamos el producto creado
        $this->client->request(
            'PUT',
            "/api/products/{$id}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name'  => 'Nombre actualizado',
                'price' => 99.99
            ])
        );

        $response = $this->client->getResponse();
        $data     = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Nombre actualizado', $data['name']);
        $this->assertEquals(99.99, $data['price']);
    }

    public function testUpdateProductValidationError(): void
    {
        // Nombre demasiado corto y precio negativo para provocar un error de validación
        $this->client->request(
            'PUT',
            '/api/products/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json'
            ],
            json_encode(['name' => 'Ab', 'price' => -5])
        );

        // Symfony con MapRequestPayload devuelve 422 Unprocessable Entity si falla el DTO
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('violations', $data);
    }

    public function testUpdateProductNotFound(): void
    {
        $this->client->request(
            'PUT',
            '/api/products/99999',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'New Name', 'price' => 20.0])
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Product with ID 99999 not found', $data['error']);
    }
}
