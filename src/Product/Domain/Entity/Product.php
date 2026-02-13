<?php

namespace Flat101\Product\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    #[Groups(['product:read'])]
    private string $name;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['product:read'])]
    private float $price;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['product:read'])]
    private DateTimeInterface $createdAt;

    public function __construct(string $name, float $price)
    {
        $this->setName($name);
        $this->setPrice($price);
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        if (strlen($name) < 3) {
            throw new InvalidArgumentException('Name must be at least 3 characters long');
        }

        $this->name = $name;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        if ($price <= 0) {
            throw new InvalidArgumentException('Price must be greater than 0');
        }

        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function update(string $name, float $price): self
    {
        $this->setName($name);
        $this->setPrice($price);

        return $this;
    }
}
