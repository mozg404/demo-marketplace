<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class UserAvatarData extends Data
{
    public function __construct(
        public string $thumb,
        public string $thumb_2,
        public string $medium,
        public string $large,
        public string $original,
    ) {
    }
}
