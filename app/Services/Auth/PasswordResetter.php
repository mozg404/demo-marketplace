<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\PasswordResetException;
use App\Models\User;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;

class PasswordResetter
{
    public function sendResetPasswordNotification(Email $email): void
    {
        $status = PasswordFacade::sendResetLink([
            'email' => $email->value
        ]);

        if ($status !== PasswordFacade::RESET_LINK_SENT) {
            $this->throwException($status);
        }
    }

    public function reset(Email $email, Password $password, Password $passwordConfirmation, string $token): void
    {
        $status = PasswordFacade::reset(
            [
                'email' => $email->value,
                'token' => $token,
                'password' => $password->value,
                'password_confirmation' => $passwordConfirmation->value,
            ],
            static function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== PasswordFacade::PASSWORD_RESET) {
            $this->throwException($status);
        }
    }

    protected function throwException(string $status): void
    {
        throw new PasswordResetException($status, __($status));
    }
}