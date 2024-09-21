<?php declare(strict_types = 1);

namespace App\Service\Collection;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;

class FoodCollectionService implements CollectionInterface
{
    public function __construct(
        protected readonly FoodRepository $repository, 
        protected readonly EntityManagerInterface $entityManager
    ) {}

    public function add(Food $food): void
    {
        $this->entityManager->persist($food);
        $this->entityManager->flush();
    }

    public function remove(Food $food): void
    {
        $this->entityManager->remove($food);
        $this->entityManager->flush();
    }

    public function list(): array
    {
        return $this->repository->findAll();
    }
}