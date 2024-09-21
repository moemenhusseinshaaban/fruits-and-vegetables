<?php declare(strict_types = 1);

namespace App\Service\Collection;

use App\Entity\Food;

interface CollectionInterface
{
    public function add(Food $food): void;

    public function remove(Food $food): void;

    public function list(): array;
}
