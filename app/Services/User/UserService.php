<?php

namespace App\Services\User;

use App\DTO\User\UserUpdateAvatarDto;
use App\DTO\User\UserUpdatePasswordDto;
use App\DTO\User\UserUpdateProfileDto;
use App\Exceptions\Auth\InvalidPasswordException;
use App\Exceptions\User\EmailAlreadyExistsException;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

readonly class UserService
{
    public function __construct(
        private UserQuery $userQuery,
    ) {
    }

    public function create(string $name, string $email, string $hashedPassword, bool $emailVerified = false, bool $isAdmin = false, ?Carbon $createdAt = null): User
    {
        if ($this->userQuery->checkExistsByEmail($email)) {
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

    public function updateProfile(User $user, UserUpdateProfileDto $dto): void
    {
        $user->name = $dto->name;
        $user->save();
    }
    
    public function updatePassword(User $user, UserUpdatePasswordDto $dto): void
    {
        if (Hash::check($dto->old_password, $user->password)) {
            throw new InvalidPasswordException();
        }

        $user->password = Hash::make($dto->password);
        $user->save();
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