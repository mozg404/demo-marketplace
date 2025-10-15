<?php

namespace App\ValueObjects;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as RulesPassword;
use InvalidArgumentException;

readonly class Password
{
    public function __construct(
        public string $value
    ) {
        // Валидируем пароль по стандартным правилам Laravel
        $validator = validator(
            ['password' => $value],
            ['password' => [RulesPassword::default()]]
        );

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                $validator->errors()->first('password')
            );
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function hashed(): string
    {
        return Hash::make($this->value);
    }
}