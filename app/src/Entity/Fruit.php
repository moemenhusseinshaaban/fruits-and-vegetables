<?php 

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Fruit extends Food
{
    public function __construct(string $name, int $quantityInGrams)
    {
        parent::__construct($name, $quantityInGrams);
    }
}
