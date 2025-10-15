<?php

namespace App\Services\User;

use App\Builders\UserQueryBuilder;
use App\Models\User;

class UserQuery
{
    public function query(): UserQueryBuilder
    {
        return User::query();
    }

    public function get(int $id): User
    {
        return User::findOrFail($id);
    }

    public function getByEmail(string $email): User
    {
        $user = $this->query()->findByEmail($email);

        if (!$user) {
            throw new \InvalidArgumentException("User with email {$email} not found");
        }

        return $user;
    }

    public function getRandomUser(): User
    {
        return $this->query()->withoutAdmin()->inRandomOrder()->first();
    }
}