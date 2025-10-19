<?php

namespace App\DTO\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;

class AuthForgotPasswordDTO extends Data
{
    public function __construct(
        #[Email]
        public string $email,
    ) {
    }
}