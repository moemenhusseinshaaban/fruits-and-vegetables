<?php

declare(strict_types=1);

namespace App\Tests\Service\Resolver;

use App\Enum\FoodType;
use App\Service\Collection\FruitCollectionService;
use App\Service\Collection\VegetableCollectionService;
use App\Service\Resolver\CollectionResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class CollectionResolverTest extends TestCase
{
    private CollectionResolver $collectionResolver;
    private $serviceLocator;

    protected function setUp(): void
    {
        $this->serviceLocator = $this->createMock(ServiceLocator::class);
        $this->setUpCollectionResolver();
    }

    protected function setUpCollectionResolver(): void
    {
        $fruitCollectionServiceMock = $this->createMock(FruitCollectionService::class);
        $vegetableCollectionServiceMock = $this->createMock(VegetableCollectionService::class);

        $this->serviceLocator->method('get')
            ->willReturnCallback(function ($class) use ($fruitCollectionServiceMock, $vegetableCollectionServiceMock) {
                return match ($class) {
                    FruitCollectionService::class => $fruitCollectionServiceMock,
                    VegetableCollectionService::class => $vegetableCollectionServiceMock,
                    default => throw new \InvalidArgumentException("Unknown service: $class"),
                };
            });

        $this->collectionResolver = new CollectionResolver($this->serviceLocator);
    }

    public function testResolveReturnsFruitCollectionService(): void
    {
        $result = $this->collectionResolver->resolve(FoodType::FRUIT->value);

        $this->assertInstanceOf(FruitCollectionService::class, $result);
    }

    public function testResolveReturnsVegetableCollectionService(): void
    {
        $result = $this->collectionResolver->resolve(FoodType::VEGETABLE->value);

        $this->assertInstanceOf(VegetableCollectionService::class, $result);
    }

    public function testResolveThrowsExceptionForUnknownType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown collection type ');

        $this->collectionResolver->resolve(null);
    }
}
