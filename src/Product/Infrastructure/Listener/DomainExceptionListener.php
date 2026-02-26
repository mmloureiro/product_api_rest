<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Listener;

use App\Product\Domain\Exception\ProductNotFoundException;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
class DomainExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ProductNotFoundException) {
            $response = new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                    'code' => Response::HTTP_NOT_FOUND
                ],
                Response::HTTP_NOT_FOUND
            );

            $event->setResponse($response);
            return;
        }

        if ($exception instanceof InvalidArgumentException) {
            $response = new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                    'code' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );

            $event->setResponse($response);
            return;
        }
    }
}

