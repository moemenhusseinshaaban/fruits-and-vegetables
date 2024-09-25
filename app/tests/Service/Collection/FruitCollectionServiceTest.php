<?php

declare(strict_types=1);

namespace App\Tests\Service\Collection;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use App\Service\Collection\FruitCollectionService;
use App\Service\Validator\FoodValidator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class FruitCollectionServiceTest extends TestCase
{
    private $fruitRepository;
    private $entityManager;
    private $foodValidator;
    private FruitCollectionService $fruitCollectionService;

    protected function setUp(): void
    {
        /** @var FruitRepository $fruitRepository */
        $this->fruitRepository = $this->createMock(FruitRepository::class);
        /** @var EntityManagerInterface $entityManager */
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        /** @var FoodValidator $foodValidator */
        $this->foodValidator = $this->createMock(FoodValidator::class);

        $this->fruitCollectionService = new FruitCollectionService(
            $this->fruitRepository,
            $this->entityManager,
            $this->foodValidator
        );
    }

    public function testAdd(): void
    {
        $name = 'Apple';
        $quantity = 1.5;
        $externalId = 100;

        $this->foodValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf(Fruit::class));

        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(Fruit::class));
        $this->entityManager->expects($this->once())->method('flush');

        $result = $this->fruitCollectionService->add($name, $quantity, $externalId);

        $this->assertInstanceOf(Fruit::class, $result);
        $this->assertSame($name, $result->getName());
        $this->assertSame($quantity, $result->getQuantityInGrams());
        $this->assertSame($externalId, $result->getExternalId());
    }

    public function testRemove(): void
    {
        /** @var Fruit $fruit */
        $fruit = $this->createMock(Fruit::class);

        $this->entityManager->expects($this->once())->method('remove')->with($fruit);
        $this->entityManager->expects($this->once())->method('flush');

        $this->fruitCollectionService->remove($fruit);
    }

    public function testList(): void
    {
        $filter = ['name' => 'Apple'];

        $expected = [$this->createMock(Fruit::class)];
        $this->fruitRepository->expects($this->once())
            ->method('findByCriteria')
            ->with($filter)
            ->willReturn($expected);

        $result = $this->fruitCollectionService->list($filter);
        $this->assertSame($expected, $result);
    }
}
