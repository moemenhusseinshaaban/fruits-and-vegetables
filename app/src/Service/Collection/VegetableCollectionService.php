<?php

declare(strict_types=1);

namespace App\Service\Collection;

use App\Entity\Food;
use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use App\Service\Validator\FoodValidator;
use Doctrine\ORM\EntityManagerInterface;

class VegetableCollectionService extends FoodCollectionService implements FoodTypeCollectionInterface
{
    public function __construct(
        VegetableRepository $vegetableRepository,
        EntityManagerInterface $entityManager,
        private readonly FoodValidator $foodValidator
    ) {
        parent::__construct($vegetableRepository, $entityManager);
    }

    public function add(?string $name, ?float $quantity, ?int $externalId): Food
    {
        $vegetable = new Vegetable($name, $quantity, $externalId);

        $this->foodValidator->validate($vegetable);

        $this->entityManager->persist($vegetable);
        $this->entityManager->flush();

        return $vegetable;
    }
}
