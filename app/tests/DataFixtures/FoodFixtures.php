<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\Entity\Food;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use Doctrine\ORM\EntityManagerInterface;

class FoodFixtures
{
    public function load(EntityManagerInterface $entityManager): void
    {
        $fruit1 = new Fruit('Apple', 1000, 1);
        $entityManager->persist($fruit1);

        $fruit2 = new Fruit('Banana', 500, 2);
        $entityManager->persist($fruit2);

        $vegetable1 = new Vegetable('Carrot', 400, 3);
        $entityManager->persist($vegetable1);

        $entityManager->flush();
    }

    public function clear(EntityManagerInterface $entityManager): void
    {
        $foodRepository = $entityManager->getRepository(Food::class);
        $foods = $foodRepository->findAll();

        foreach ($foods as $food) {
            $entityManager->remove($food);
        }

        $entityManager->flush();
    }
}
