<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Services\Auth\EmailVerificator;
use App\Services\Auth\PasswordHasher;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Support\Carbon;
use Webmozart\Assert\Assert;

readonly class UserCreator
{
    public function __construct(
        private UserChecker $userChecker,
        private PasswordHasher $hasher,
        private EmailVerificator $verificator,
    ) {
    }

    public function create(string $name, Email $email, Password $password, bool $emailVerified = true, bool $isAdmin = false, ?Carbon $createdAt = null): User
    {
        Assert::stringNotEmpty($name);
        Assert::minLength($name, 3);
        Assert::maxLength($name, 255);

        $this->userChecker->ensureUniqueEmail($email->value);

        $user = new User();
        $user->name = $name;
        $user->email = $email->value;
        $user->password = $this->hasher->hash($password->value);

        if ($isAdmin) {
            $user->is_admin = true;
        }

        if ($createdAt) {
            $user->created_at = $createdAt;
        }

        $user->save();

        if ($emailVerified) {
            $this->verificator->verifyWithoutToken($user);
        }

        return $user;
    }
}
