<?php declare(strict_types = 1);

namespace App\Service\Resolver;

use App\Enum\FoodType;
use App\Service\Collection\FruitCollectionService;
use App\Service\Collection\VegetableCollectionService;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class CollectionResolver implements ServiceSubscriberInterface
{
    public function __construct(private readonly ServiceLocator $serviceLocator)
    {
    }

    public function resolve(FoodType $type): object
    {
        return match ($type) {
            FoodType::FRUIT => $this->serviceLocator->get(FruitCollectionService::class),
            FoodType::VEGETABLE => $this->serviceLocator->get(VegetableCollectionService::class),
            default => throw new \InvalidArgumentException("Unknown collection type $type"),
        };
    }

    public static function getSubscribedServices(): array
    {
        return [
            FruitCollectionService::class,
            VegetableCollectionService::class,
        ];
    }
}
