<?php declare(strict_types = 1);

namespace App\Enum;

use App\Entity\Food;

enum FoodType: string
{
    case FRUIT = 'fruit';
    case VEGETABLE = 'vegetable';
}
