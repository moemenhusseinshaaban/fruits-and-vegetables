<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Food;
use App\Helper\UnitConversionHelper;
use App\Service\Resolver\CollectionResolver;

class FoodFactory
{
    public function __construct(
        private readonly CollectionResolver $collectionResolver,
        private readonly UnitConversionHelper $unitConversionHelper
    ) {
    }

    public function create(array $item): Food
    {
        $externalId = $item['id'] ?? null;
        $name = $item['name'] ?? null;
        $type = $item['type'] ?? null;
        $quantity = $this->unitConversionHelper->convertToGrams($item['quantity'], $item['unit']);

        $collectionService = $this->collectionResolver->resolve($type);

        $food = $collectionService->add($name, $quantity, $externalId);

        return $food;
    }
}
