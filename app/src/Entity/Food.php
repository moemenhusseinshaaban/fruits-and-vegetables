<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'float')]
    private float $quantityInGrams;

    public function __construct(string $name, float $quantityInGrams)
    {
        $this->name = $name;
        $this->quantityInGrams = $quantityInGrams;
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
}
