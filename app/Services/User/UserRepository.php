<?php

namespace App\Services\User;

use App\Builders\UserQueryBuilder;
use App\Exceptions\User\EmailAlreadyExistsException;
use App\Models\User;

class UserRepository
{
    public function query(): UserQueryBuilder
    {
        return User::query();
    }

    public function create(string $name, string $email, string $hashedPassword, bool $isAdmin = false): User
    {
        if ($this->checkExistsByEmail($email)) {
            throw new EmailAlreadyExistsException();
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $hashedPassword;

        if ($isAdmin) {
            $user->is_admin = true;
        }

        $user->save();

        return $user;
    }

    public function get(int $id): User
    {
        return $this->query()->findOrFail($id);
    }

    public function checkExistsByEmail(string $email): bool
    {
        return $this->query()->where('email', $email)->exists();
    }

    public function getUserByEmail(string $email): User
    {
        $user = $this->query()->findByEmail($email);

        if (!$user) {
            throw new \InvalidArgumentException("User with email {$email} not found");
        }

        return $user;
    }
}