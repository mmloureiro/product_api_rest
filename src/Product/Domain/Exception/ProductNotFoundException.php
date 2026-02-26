<?php

declare(strict_types=1);

namespace App\Product\Domain\Exception;

use DomainException;

class ProductNotFoundException extends DomainException
{
    public static function fromId(int $id): self
    {
        return new self(sprintf('Product with ID %d not found', $id));
    }
}
