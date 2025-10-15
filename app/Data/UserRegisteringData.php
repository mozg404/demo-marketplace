<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class UserRegisteringData extends Data
{
    public function __construct(
        #[Min(3),Max(255)]
        public string $name,
        #[Min(5),Max(255),Email,Unique('users', 'email')]
        public string $email,
        #[Min(4),Max(255)]
        public string $password,
    )
    {
        $this->name = Str::ucfirst(Str::trim($this->name));
        $this->email = Str::lower(Str::trim($this->email));
    }
}
