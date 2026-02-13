<?php

declare(strict_types=1);

namespace Flat101\Product\Infrastructure\Controller;

use Flat101\Product\Application\Update\UpdateProductUseCase;
use Flat101\Product\Infrastructure\Dto\ProductRequestDto;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/products/{id}', name: 'api_products_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
class UpdateProductController extends AbstractController
{
    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Update an existing product',
        tags: ['Product']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID of the product to update',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Updated product data',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Producto Actualizado'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 39.99)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Product updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Producto Actualizado'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 39.99),
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
        int $id,
        #[MapRequestPayload] ProductRequestDto $dto,
        UpdateProductUseCase $useCase,
        SerializerInterface $serializer
    ): JsonResponse {
        try {
            $product = $useCase->execute($id, $dto->name, $dto->price);

            return new JsonResponse(
                $serializer->serialize($product, 'json', ['groups' => 'product:read']),
                Response::HTTP_OK,
                [],
                true
            );
        } catch (NotFoundHttpException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage(), 'code' => Response::HTTP_NOT_FOUND],
                Response::HTTP_NOT_FOUND
            );
        }
    }
}
