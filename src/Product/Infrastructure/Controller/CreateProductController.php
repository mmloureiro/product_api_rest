<?php

namespace Flat101\Product\Infrastructure\Controller;

use Exception;
use Flat101\Product\Application\Create\CreateProductUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/products', name: 'api_products_create', methods: ['POST'])]
class CreateProductController extends AbstractController
{
    #[OA\Post(
        path: '/api/products',
        summary: 'Create a new product',
        tags: ['Products']
    )]
    #[OA\RequestBody(
        description: 'Product data',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Producto Ejemplo'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99)
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Product created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Producto Ejemplo'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-02-11T10:30:00+00:00'
                )
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid input data',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Missing name or price')
            ]
        )
    )]
    public function __invoke(
        Request $request,
        CreateProductUseCase $useCase,
        SerializerInterface $serializer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['price'])) {
            return new JsonResponse(['error' => 'Missing name or price'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $product = $useCase->execute($data ['name'], (float)$data['price']);

            return new JsonResponse(
                $serializer->serialize($product, 'json', ['groups' => 'product:read']),
                Response::HTTP_CREATED, [], true
            );
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
