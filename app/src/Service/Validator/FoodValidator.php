<?php declare(strict_types=1);

namespace App\Service\Validator;

use App\Entity\Food;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class FoodValidator
{
    public function __construct(private readonly ValidatorInterface $validator) {}

    public function validate(Food $food): void
    {
        $errors = $this->validator->validate($food);

        if (count($errors) > 0) {
            throw new ValidationFailedException($food, $errors);
        }
    }
}
