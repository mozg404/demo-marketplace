<?php

namespace App\Services\User;

use App\Models\User;

class UserQuery
{
    public function getRandomUser(): ?User
    {
        return User::query()->withoutAdmin()->inRandomOrder()->first();
    }

    public function checkExistsByEmail(string $email): bool
    {
        return User::query()->where('email', $email)->exists();
    }

    public function getUserByEmail(string $email): User
    {
        $user = User::query()->findByEmail($email);

        if (!$user) {
            throw new \InvalidArgumentException("Пользователь с email {$email} не найден");
        }

        return $user;
    }
}