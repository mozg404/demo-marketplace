<?php

namespace App\Services\User;

use App\Builders\UserQueryBuilder;
use App\Exceptions\User\EmailAlreadyExistsException;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserRepository
{
    public function query(): UserQueryBuilder
    {
        return User::query();
    }

    public function create(string $name, string $email, string $hashedPassword, bool $emailVerified = false, bool $isAdmin = false, ?Carbon $createdAt = null): User
    {
        if ($this->checkExistsByEmail($email)) {
            throw new EmailAlreadyExistsException();
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $hashedPassword;

        if ($emailVerified) {
            $user->email_verified_at = $user->freshTimestamp();
        }

        if ($isAdmin) {
            $user->is_admin = true;
        }

        if ($createdAt) {
            $user->created_at = $createdAt;
        }

        $user->save();

        return $user;
    }

    public function getRandomUser(): ?User
    {
        return $this->query()->withoutAdmin()->inRandomOrder()->first();
    }

    public function updateName(User $user, string $name): void
    {
        $user->name = $name;
        $user->save();
    }

    public function updatePassword(User $user, string $hashedPassword): void
    {
        $user->password = $hashedPassword;
        $user->save();
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