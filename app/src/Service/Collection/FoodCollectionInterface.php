<?php

declare(strict_types=1);

namespace App\Service\Collection;

use App\Entity\Food;

interface FoodCollectionInterface
{
    public function remove(Food $food): void;

    public function list(array $filter): array;
}
