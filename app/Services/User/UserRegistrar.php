<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Events\UserRegistered;
use App\Models\User;
use App\ValueObjects\Email;
use App\ValueObjects\Password;

readonly class UserRegistrar
{
    public function __construct(
        private UserCreator $creator,
    ) {
    }

    public function register(string $name, Email $email, Password $password): User
    {
        $user = $this->creator->create($name, $email, $password);

        event(new UserRegistered($user));

        return $user;
    }
}
