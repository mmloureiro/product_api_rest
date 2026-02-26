<?php

namespace App\Product\Infrastructure\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class ProductRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Name is required")]
        #[Assert\Length(min: 3, minMessage: "The name has to be at least {{ limit }} characters long")]
        public string $name,

        #[Assert\NotBlank(message: "Price is required")]
        #[Assert\Type(type: "float", message: "The price must be a valid number")]
        #[Assert\Positive(message: "The price must be a positive number")]
        public float $price,
    ) {
    }
}
