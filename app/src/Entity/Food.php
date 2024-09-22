<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping as ORM;
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
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        #[Assert\NotBlank(message: "Name cannot be null.")]
        #[Assert\Length(max: 255)]
        private ?string $name,

        #[ORM\Column(type: 'float')]
        #[Assert\NotNull(message: "Quantity must be provided.")]
        #[Assert\Positive(message: "Quantity must be a positive number.")]
        private ?float $quantityInGrams
    ) {}

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
}
