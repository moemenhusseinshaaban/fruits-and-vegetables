<?php declare(strict_types = 1);

namespace App\Factory;

use App\Entity\Food;
use App\Enum\FoodType;
use App\Helper\UnitConversionHelper;
use App\Service\Resolver\CollectionResolver;
use App\Service\Validator\FoodValidator;

class FoodFactory
{
    public function __construct(
        private readonly CollectionResolver $collectionResolver,
        private readonly UnitConversionHelper $unitConversionHelper
    ) {}

    public function create(array $item): Food
    {
        $name = $item['name'] ?? null;
        $type = $item['type'] ?? null;
        $quantity = $this->unitConversionHelper->convertToGrams($item['quantity'], $item['unit']);

        $collectionService = $this->collectionResolver->resolve($type);

        $food = $collectionService->add($name, $quantity);

        return $food;
    }
}
