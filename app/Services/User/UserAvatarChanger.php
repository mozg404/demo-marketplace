<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Http\UploadedFile;

class UserAvatarChanger
{
    public function changeFromUploadedFile(User $user, UploadedFile $image): void
    {
        $user->clearMediaCollection($user::MEDIA_COLLECTION_AVATAR);
        $user->addMedia($image)->toMediaCollection($user::MEDIA_COLLECTION_AVATAR);
    }

    public function changeFromPath(User $user, string $path): void
    {
        $user->clearMediaCollection($user::MEDIA_COLLECTION_AVATAR);
        $user->addMedia($path)
            ->preservingOriginal()
            ->toMediaCollection($user::MEDIA_COLLECTION_AVATAR);
    }
}