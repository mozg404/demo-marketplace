<?php

namespace App\Services\Demo;

use App\Models\User;
use App\Services\User\UserRepository;
use App\Services\User\UserService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

readonly class DemoUserCreator
{
    public function __construct(
        private UserRepository $repository,
        private UserService $userService,
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
        $user = $this->repository->create($name, $email, Hash::make($password), true, $isAdmin, $createdAt);
        $this->userService->updateAvatarFromPath($user, $avatarPath);

        return $user;
    }
}