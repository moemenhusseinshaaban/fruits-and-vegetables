<?php

namespace App\Tests\Factory;

use App\Factory\FoodFactory;
use App\Helper\UnitConversionHelper;
use App\Service\Resolver\CollectionResolver;
use App\Entity\Food;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Service\Collection\FoodCollectionService;
use App\Service\Collection\FruitCollectionService;
use App\Service\Collection\VegetableCollectionService;
use PHPUnit\Framework\TestCase;

class FoodFactoryTest extends TestCase
{
    private $collectionResolver;
    private $unitConversionHelper;
    private FoodFactory $foodFactory;

    protected function setUp(): void
    {
        /** @var CollectionResolver $collectionResolver */
        $this->collectionResolver = $this->createMock(CollectionResolver::class);
        /** @var UnitConversionHelper $unitConversionHelper */
        $this->unitConversionHelper = $this->createMock(UnitConversionHelper::class);
        $this->foodFactory = new FoodFactory($this->collectionResolver, $this->unitConversionHelper);
    }

    public function testCreateFoodSuccessfully(): void
    {
        $item = [
            'id' => 1234,
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 1,
            'unit' => 'kg',
        ];

        $this->unitConversionHelper
            ->method('convertToGrams')
            ->with($item['quantity'], $item['unit'])
            ->willReturn(1000.0);

        $mockService = $this->createMock(FruitCollectionService::class);
        $mockService->expects($this->once())->method('add')
                    ->with($item['name'], 1000, $item['id'])->willReturn(new Fruit($item['name'], 1000, $item['id']));

        $this->collectionResolver
            ->method('resolve')
            ->with($item['type'])
            ->willReturn($mockService);

        $food = $this->foodFactory->create($item);

        $this->assertInstanceOf(Food::class, $food);
        $this->assertEquals($item['name'], $food->getName());
    }

    public function testThrowsInvalidArgumentForInvalidUnit(): void
    {
        $item = [
            'id' => 1234,
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 1,
            'unit' => null
        ];

        $this->unitConversionHelper
            ->method('convertToGrams')
            ->willThrowException(new \InvalidArgumentException('Unknown unit: invalid_unit'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown unit: invalid_unit');

        $this->foodFactory->create($item);
    }
}
