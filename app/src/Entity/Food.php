<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
#[ORM\Table(name: 'food')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    self::FRUIT => Fruit::class,
    self::VEGETABLE => Vegetable::class,
])]
abstract class Food
{
    const FRUIT = 'fruit';
    const VEGETABLE = 'vegetable';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['food:read', 'food-subtype:read'])]
    protected ?int $id = null;

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        #[Assert\NotBlank(message: "Name cannot be null.")]
        #[Assert\Length(max: 255)]
        #[Groups(['food:read', 'food-subtype:read'])]
        protected ?string $name,
        #[ORM\Column(type: 'float')]
        #[Assert\NotNull(message: "Quantity must be provided.")]
        #[Assert\Positive(message: "Quantity must be a positive number.")]
        #[Groups(['food:read:unit:g'])]
        protected ?float $quantityInGrams,
        #[ORM\Column(type: 'integer', unique: true)]
        #[Assert\NotNull(message: "Quantity must be provided.")]
        #[Groups(['food:read', 'food-subtype:read'])]
        protected ?int $externalId
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantityInGrams(): float
    {
        return $this->quantityInGrams;
    }

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    #[Groups(['food:read'])]
    public function getType(): string
    {
        return match (true) {
            $this instanceof Fruit => self::FRUIT,
            $this instanceof Vegetable => self::VEGETABLE,
            default => 'unknown',
        };
    }

    #[Groups(['food:read:unit:kg'])]
    public function getQuantityInKiloGrams(): float
    {
        return $this->quantityInGrams / 1000;
    }
}
