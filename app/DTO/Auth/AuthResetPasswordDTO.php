<?php

namespace App\DTO\Auth;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class AuthResetPasswordDTO extends Data
{
    public function __construct(
        #[Email]
        public string $email,
        public string $token,
        #[Password(min: 8), Max(50), Confirmed]
        public string $password,
        #[Password(min: 8), Max(50)]
        public string $password_confirmation,
    ) {
    }
}