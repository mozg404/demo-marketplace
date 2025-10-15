<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\InvalidPasswordException;
use Illuminate\Support\Facades\Hash;

class PasswordHasher
{
    public function hash(string $password): string
    {
        return Hash::make($password);
    }

    public function check(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function ensureVerifiedPassword(string $password, string $hash): void
    {
        if (!$this->check($password, $hash)) {
            throw new InvalidPasswordException();
        }
    }
}