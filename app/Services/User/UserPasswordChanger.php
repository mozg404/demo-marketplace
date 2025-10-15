<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Auth\PasswordHasher;
use App\ValueObjects\Password;

readonly class UserPasswordChanger
{
    public function __construct(
        private PasswordHasher $hasher
    ) {
    }

    public function changePassword(User $user, Password $oldPassword, Password $newPassword): void
    {
        $this->hasher->ensureVerifiedPassword($oldPassword->value, $user->password);
        $user->password = $this->hasher->hash($newPassword->value);
        $user->save();
    }
}