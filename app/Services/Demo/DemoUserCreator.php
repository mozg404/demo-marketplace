<?php

namespace App\Services\Demo;

use App\Models\User;
use App\Services\User\UserAvatarChanger;
use App\Services\User\UserCreator;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Support\Carbon;

readonly class DemoUserCreator
{
    public function __construct(
        private UserCreator $creator,
        private UserAvatarChanger $avatarChanger,
    ) {
    }

    public function createMainUser(): User
    {
        return $this->create(
            name: fake()->userName(),
            email: config('demo.main_user_email'),
            password: config('demo.main_user_password'),
            avatarPath: fake()->randomElement(include resource_path('data/user_avatars.php')),
            isAdmin: true,
            createdAt: new Carbon(fake()->dateTimeBetween('-1 year'))
        );
    }

    public function createRandomUser(): User
    {
        return $this->create(
            name: fake()->userName(),
            email: fake()->unique()->email(),
            password: config('demo.random_user_password'),
            avatarPath: fake()->randomElement(include resource_path('data/user_avatars.php')),
            createdAt: new Carbon(fake()->dateTimeBetween('-1 year'))
        );
    }

    public function create(string $name, string $email, string $password, string $avatarPath, bool $isAdmin = false, ?Carbon $createdAt = null): User
    {
        $user = $this->creator->create(
            name: $name,
            email: new Email($email),
            password: new Password($password),
            emailVerified: true,
            isAdmin: $isAdmin,
            createdAt: $createdAt
        );
        $this->avatarChanger->changeFromPath($user, $avatarPath);

        return $user;
    }
}