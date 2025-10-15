<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\AuthenticationFailedException;
use App\Models\User;
use App\Services\User\UserQuery;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

readonly class Authenticator
{
    public function __construct(
        private EmailVerificator $emailVerificator,
        private Session $session,
        private UserQuery $userQuery,
    ) {
    }

    /**
     * @throws AuthenticationFailedException
     */
    public function authenticate(Email $email, Password $password, bool $remember = true): void
    {
        if (!$this->validate($email, $password)) {
            throw new AuthenticationFailedException();
        }

        $user = $this->userQuery->getByEmail($email);
        $this->emailVerificator->ensureVerifiedEmail($user);
        $this->login($user, $remember);
    }
    
    public function login(User $user, bool $remember = true): void
    {
        Auth::login($user, true);
    }

    public function logout(): void
    {
        Auth::logout();
        $this->session->regenerate();
        $this->session->regenerateToken();
    }

    public function validate(Email $email, Password $password): bool
    {
        return Auth::validate([
            'email' => $email->value,
            'password' => $password->value,
        ]);
    }
}