<?php declare(strict_types = 1);

namespace App\Service\Collection;

use App\Repository\VegetableRepository;
use Doctrine\ORM\EntityManagerInterface;

class VegetableCollectionService extends FoodCollectionService
{
    public function __construct(VegetableRepository $vegetableRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($vegetableRepository, $entityManager);
    }
}

