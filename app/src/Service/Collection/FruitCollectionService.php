<?php declare(strict_types = 1);

namespace App\Service\Collection;

use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;

class FruitCollectionService extends FoodCollectionService
{
    public function __construct(FruitRepository $fruitRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($fruitRepository, $entityManager);
    }
}

