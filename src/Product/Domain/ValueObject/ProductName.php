<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use InvalidArgumentException;

final readonly class ProductName
{
    public function __construct(
        private string $value
    ) {
        if (strlen($this->value) < 3) {
            throw new InvalidArgumentException('Name must be at least 3 characters long');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
