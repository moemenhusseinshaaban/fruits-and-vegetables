<?php declare(strict_types=1);

namespace App\Helper;

use App\Enum\Unit;

class UnitConversionHelper
{
    public function convertToGrams(?float $quantity, ?string $unit): float
    {
        if ($quantity) {
            return match ($unit) {
                Unit::KILO_GRAM->value => $quantity * 1000,
                Unit::GRAM->value => $quantity,
                default => throw new \InvalidArgumentException("Unknown unit: $unit"),
            };
        }

        return 0;
    }
}
