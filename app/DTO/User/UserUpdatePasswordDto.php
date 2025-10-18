<?php

namespace App\DTO\User;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class UserUpdatePasswordDto extends Data
{
    public function __construct(
        #[Password(min: 8), Max(50)]
        public string $old_password,
        #[Password(min: 8), Max(50), Confirmed]
        public string $password,
    ) {
    }
}