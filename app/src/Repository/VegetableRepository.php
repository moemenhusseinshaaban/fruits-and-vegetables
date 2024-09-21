<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Vegetable;
use Doctrine\Persistence\ManagerRegistry;

class VegetableRepository extends FoodRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vegetable::class);
    }
}
