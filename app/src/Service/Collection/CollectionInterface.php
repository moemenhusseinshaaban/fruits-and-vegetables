<?php declare(strict_types = 1);

namespace App\Service\Collection;

use App\Entity\Food;

interface CollectionInterface
{
    public function add(?string $name, ?float $quantity, ?int $externalId): Food;

    public function remove(Food $food): void;

    public function list(): array;
}
