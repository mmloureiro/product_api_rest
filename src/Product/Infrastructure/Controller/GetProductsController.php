<?php

declare(strict_types=1);

namespace Flat101\Product\Infrastructure\Controller;

use Flat101\Product\Application\List\ListProductsUseCase;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products', name: 'api_products_list', methods: ['GET'])]
class GetProductsController extends AbstractController
{
    public function __construct(
        private readonly ListProductsUseCase $useCase
    ) {}

    #[OA\Get(
        path: '/api/products',
        summary: 'Get all products',
        tags: ['Product']
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of products',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
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
        )
    )]
    public function __invoke(): JsonResponse
    {
        $products = $this->useCase->execute();

        return new JsonResponse($products, Response::HTTP_OK);
    }
}
