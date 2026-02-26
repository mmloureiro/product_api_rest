<?php

declare(strict_types=1);

namespace Flat101\Product\Domain\ValueObject;

use InvalidArgumentException;

final readonly class ProductPrice
{
    private int $amountInCents;

    public function __construct(float $amount)
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Price must be greater than 0');
        }

        $this->amountInCents = (int) round($amount * 100);
    }

    public function amount(): float
    {
        return $this->amountInCents / 100;
    }

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }
}
