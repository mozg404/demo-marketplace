<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\EmailVerification\EmailAlreadyVerifiedException;
use App\Exceptions\Auth\EmailVerification\EmailNotVerifiedException;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class EmailVerificator
{
    public function sendVerificationNotification(User $user): void
    {
        $this->ensureNotVerifiedEmail($user);
        $user->sendEmailVerificationNotification();
    }
    
    public function verify(User $user, string $hash): void
    {
        $this->ensureNotVerifiedEmail($user);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            throw new InvalidSignatureException();
        }

        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    public function verifyWithoutToken(User $user): void
    {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    public function hasVerifiedEmail(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function ensureVerifiedEmail(User $user): void
    {
        if (!$this->hasVerifiedEmail($user)) {
            throw new EmailNotVerifiedException();
        }
    }

    protected function ensureNotVerifiedEmail(User $user): void
    {
        if ($this->hasVerifiedEmail($user)) {
            throw new EmailAlreadyVerifiedException("Email $user->email уже подтвержден");
        }
    }
}