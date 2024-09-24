<?php declare(strict_types = 1);

namespace App\Service\Collection;

use App\Entity\Food;

interface FoodTypeCollectionInterface
{
    public function add(?string $name, ?float $quantity, ?int $externalId): Food;
}
