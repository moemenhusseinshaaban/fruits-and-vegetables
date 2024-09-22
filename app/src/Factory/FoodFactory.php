<?php declare(strict_types = 1);

namespace App\Factory;

use App\Entity\Food;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Enum\FoodType;
use App\Enum\Unit;
use App\Helper\UnitConversionHelper;
use App\Service\Resolver\CollectionResolver;
use App\Service\Validator\FoodValidator;

class FoodFactory
{
    public function __construct(
        private readonly CollectionResolver $collectionResolver,
        private readonly FoodValidator $foodValidator,
        private readonly UnitConversionHelper $unitConversionHelper
    ) {}

    public function create(array $item): Food
    {
        $name = $item['name'];
        $type = $item['type'];
        $quantity = $this->unitConversionHelper->convertToGrams($item['quantity'], $item['unit']);

        $food = match ($type) {
            FoodType::FRUIT->value => new Fruit($name, $quantity),
            FoodType::VEGETABLE->value => new Vegetable($name, $quantity),
            default => throw new \InvalidArgumentException("Unknown type: $type"),
        };

        $this->foodValidator->validate($food);

        $collectionService = $this->collectionResolver->resolve(FoodType::from($type));
        $collectionService->add($food);

        return $food;
    }
}
