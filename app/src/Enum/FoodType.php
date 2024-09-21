<?php

namespace App\Enum;

use App\Entity\Food;

enum FoodType: string
{
    case FRUIT = 'fruit';
    case VEGETABLE = 'vegetable';
}
