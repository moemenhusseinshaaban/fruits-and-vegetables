<?php

declare(strict_types=1);

namespace App\Service\Collection;

use App\Entity\Food;
use App\Entity\Fruit;
use App\Repository\FruitRepository;
use App\Service\Validator\FoodValidator;
use Doctrine\ORM\EntityManagerInterface;

class FruitCollectionService extends FoodCollectionService implements FoodTypeCollectionInterface
{
    public function __construct(
        FruitRepository $fruitRepository,
        EntityManagerInterface $entityManager,
        private readonly FoodValidator $foodValidator
    ) {
        parent::__construct($fruitRepository, $entityManager);
    }

    public function add(?string $name, ?float $quantity, ?int $externalId): Food
    {
        $fruit = new Fruit($name, $quantity, $externalId);

        $this->foodValidator->validate($fruit);

        $this->entityManager->persist($fruit);
        $this->entityManager->flush();

        return $fruit;
    }
}
