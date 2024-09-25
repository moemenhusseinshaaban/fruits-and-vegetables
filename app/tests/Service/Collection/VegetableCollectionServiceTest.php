<?php

declare(strict_types=1);

namespace App\Tests\Service\Collection;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use App\Service\Collection\VegetableCollectionService;
use App\Service\Validator\FoodValidator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class VegetableCollectionServiceTest extends TestCase
{
    private $vegetableRepository;
    private $entityManager;
    private $foodValidator;
    private VegetableCollectionService $vegetableCollectionService;

    protected function setUp(): void
    {
        /** @var VegetableRepository $vegetableRepository */
        $this->vegetableRepository = $this->createMock(VegetableRepository::class);
        /** @var EntityManagerInterface $entityManager */
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        /** @var FoodValidator $foodValidator */
        $this->foodValidator = $this->createMock(FoodValidator::class);

        $this->vegetableCollectionService = new VegetableCollectionService(
            $this->vegetableRepository,
            $this->entityManager,
            $this->foodValidator
        );
    }

    public function testAdd(): void
    {
        $name = 'Carrot';
        $quantity = 2.0;
        $externalId = 200;

        $this->foodValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf(Vegetable::class));

        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(Vegetable::class));
        $this->entityManager->expects($this->once())->method('flush');

        $result = $this->vegetableCollectionService->add($name, $quantity, $externalId);

        $this->assertInstanceOf(Vegetable::class, $result);
        $this->assertSame($name, $result->getName());
        $this->assertSame($quantity, $result->getQuantityInGrams());
        $this->assertSame($externalId, $result->getExternalId());
    }

    public function testRemove(): void
    {
        /** @var Vegetable $vegetable */
        $vegetable = $this->createMock(Vegetable::class);

        $this->entityManager->expects($this->once())->method('remove')->with($vegetable);
        $this->entityManager->expects($this->once())->method('flush');

        $this->vegetableCollectionService->remove($vegetable);
    }

    public function testList(): void
    {
        $filter = ['name' => 'Apple'];

        $expected = [$this->createMock(Vegetable::class)];
        $this->vegetableRepository->expects($this->once())
            ->method('findByCriteria')
            ->with($filter)
            ->willReturn($expected);

        $result = $this->vegetableCollectionService->list($filter);
        $this->assertSame($expected, $result);
    }
}
