<?php

namespace App\DTO\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class AuthLoginDTO extends Data
{
    public function __construct(
        #[Email]
        public string $email,
        #[Password(min:8), Max(50)]
        public string $password
    ) {
    }
}