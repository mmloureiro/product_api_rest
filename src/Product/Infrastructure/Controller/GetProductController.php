<?php

declare(strict_types=1);

namespace Flat101\Product\Infrastructure\Controller;

use Flat101\Product\Application\Find\FindProductUseCase;
use Flat101\Product\Domain\Exception\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/products/{id}', name: 'api_products_get', methods: ['GET'])]
class GetProductController extends AbstractController
{
    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get details of a product',
        tags: ['Product'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID of the product to retrieve',
                schema: new OA\Schema(type: 'integer')
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the product details',
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
        response: 404,
        description: 'Product not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Product not found'),
                new OA\Property(property: 'code', type: 'integer', example: 404)
            ]
        )
    )]
    public function __invoke(
        int $id,
        FindProductUseCase $useCase
    ): JsonResponse {
        try {
            $productResponse = $useCase->execute($id);

            return new JsonResponse($productResponse, Response::HTTP_OK);
        } catch (ProductNotFoundException $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                    'code'  => Response::HTTP_NOT_FOUND
                ],
                Response::HTTP_NOT_FOUND
            );
        }
    }
}
