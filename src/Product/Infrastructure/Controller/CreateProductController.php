<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use Exception;
use App\Product\Application\Create\CreateProductUseCase;
use App\Product\Infrastructure\Dto\ProductRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/products', name: 'api_products_create', methods: ['POST'])]
class CreateProductController extends AbstractController
{
    #[OA\Post(
        path: '/api/products',
        summary: 'Create a new product',
        tags: ['Product']
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
        #[MapRequestPayload] ProductRequestDto $dto,
        CreateProductUseCase $useCase
    ): JsonResponse {
        try {
            $productResponse = $useCase->execute($dto->name, $dto->price);

            return new JsonResponse($productResponse, Response::HTTP_CREATED);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
