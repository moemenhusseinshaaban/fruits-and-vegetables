<?php

declare(strict_types=1);

namespace App\Tests\Service\Collection;

use App\Entity\Food;
use App\Repository\FoodRepository;
use App\Service\Collection\FoodCollectionService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class FoodCollectionServiceTest extends TestCase
{
    private $repository;
    private $entityManager;
    private FoodCollectionService $foodCollectionService;

    protected function setUp(): void
    {
        /** @var FoodRepository $repository */
        $this->repository = $this->createMock(FoodRepository::class);
        /** @var EntityManagerInterface $entityManager */
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->foodCollectionService = new FoodCollectionService(
            $this->repository,
            $this->entityManager
        );
    }

    public function testRemove(): void
    {
        /** @var Food $food */
        $food = $this->createMock(Food::class);

        $this->entityManager->expects($this->once())->method('remove')->with($food);
        $this->entityManager->expects($this->once())->method('flush');

        $this->foodCollectionService->remove($food);
    }

    public function testList(): void
    {
        $filter = ['name' => 'Apple'];

        $expected = [$this->createMock(Food::class)];
        $this->repository->expects($this->once())
            ->method('findByCriteria')
            ->with($filter)
            ->willReturn($expected);

        $result = $this->foodCollectionService->list($filter);
        $this->assertSame($expected, $result);
    }
}
