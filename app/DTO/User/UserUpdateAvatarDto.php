<?php

namespace App\DTO\User;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class UserUpdateAvatarDto extends Data
{
    public function __construct(
        #[Image, Mimes(['image/png', 'image/jpeg', 'image/jpg', 'image/svg']), Max(5120)]
        public string $avatar,
    ) {
    }
}