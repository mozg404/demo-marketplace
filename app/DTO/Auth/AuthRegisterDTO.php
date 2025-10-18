<?php

namespace App\DTO\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class AuthRegisterDTO extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Unique('users'), Email]
        public string $email,
        #[Password(min: 8), Max(50)]
        public string $password,
    ) {
    }
}