<?php declare(strict_types = 1);

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FruitRepository::class)]
class Fruit extends Food
{
    public function __construct(string $name, int $quantityInGrams)
    {
        parent::__construct($name, $quantityInGrams);
    }
}
