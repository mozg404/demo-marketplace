<?php

namespace App\Services\User;

use App\DTO\User\UserUpdateAvatarDto;
use App\DTO\User\UserUpdatePasswordDto;
use App\DTO\User\UserUpdateProfileDto;
use App\Exceptions\Auth\InvalidPasswordException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function updateProfile(User $user, UserUpdateProfileDto $dto): void
    {
        $this->userRepository->updateName($user, $dto->name);
    }
    
    public function updatePassword(User $user, UserUpdatePasswordDto $dto): void
    {
        if (Hash::check($dto->old_password, $user->password)) {
            throw new InvalidPasswordException();
        }

        $this->userRepository->updatePassword($user, Hash::make($dto->password));
    }

    public function updateAvatar(User $user, UserUpdateAvatarDto $dto): void
    {
        $user->clearMediaCollection($user::MEDIA_COLLECTION_AVATAR);
        $user->addMedia($dto->image)->toMediaCollection($user::MEDIA_COLLECTION_AVATAR);
    }

    public function updateAvatarFromPath(User $user, string $path): void
    {
        $user->clearMediaCollection($user::MEDIA_COLLECTION_AVATAR);
        $user->addMedia($path)
            ->preservingOriginal()
            ->toMediaCollection($user::MEDIA_COLLECTION_AVATAR);
    }
}