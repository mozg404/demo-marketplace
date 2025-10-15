<?php

namespace App\Services\User;

use App\Models\User;

class UserProfileUpdater
{
    public function updateProfile(User $user, string $name): void
    {
        $user->name = $name;
        $user->save();
    }
}