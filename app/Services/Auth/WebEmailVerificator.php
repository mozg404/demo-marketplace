<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\EmailVerification\NoPendingEmailVerificationException;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class WebEmailVerificator
{
    public const string SESSION_KEY = 'auth_verification_user_id';

    public function __construct(
        private readonly EmailVerificator $verificator,
        private readonly Session $session,
    ) {
    }

    public function verify(User $user, string $hash): void
    {
        $this->verificator->verify($user, $hash);
        $this->session->forget(self::SESSION_KEY);
    }

    public function sendVerificationNotification(User $user): void
    {
        $this->verificator->sendVerificationNotification($user);
        $this->session->put(self::SESSION_KEY, $user->id);
    }

    public function resendVerificationNotification(): void
    {
        $this->verificator->sendVerificationNotification(
            $this->getVerificationUser()
        );
    }

    public function getVerificationUser(): ?User
    {
        $this->ensureVerificationInProgress();

        return User::find($this->session->get(self::SESSION_KEY));
    }

    public function ensureVerificationInProgress(): void
    {
        if (!$this->hasVerificationInProgress()) {
            throw new NoPendingEmailVerificationException();
        }
    }

    public function hasVerificationInProgress(): bool
    {
        return $this->session->has(self::SESSION_KEY);
    }
}