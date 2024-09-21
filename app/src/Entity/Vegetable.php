<?php declare(strict_types = 1);

namespace App\Entity;

use App\Repository\VegetableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VegetableRepository::class)]
class Vegetable extends Food
{
    public function __construct(string $name, int $quantityInGrams)
    {
        parent::__construct($name, $quantityInGrams);
    }
}
