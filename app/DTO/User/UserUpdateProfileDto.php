<?php

namespace App\DTO\User;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class UserUpdateProfileDto extends Data
{
    public function __construct(
        #[Min(8), Max(255)]
        public string $name,
    ) {
    }
}