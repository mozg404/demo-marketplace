<?php

namespace App\Services\Auth;

use App\DTO\Auth\AuthForgotPasswordDTO;
use App\DTO\Auth\AuthLoginDTO;
use App\DTO\Auth\AuthRegisterDTO;
use App\DTO\Auth\AuthResetPasswordDTO;
use App\Exceptions\Auth\AuthenticationFailedException;
use App\Exceptions\Auth\EmailVerification\EmailAlreadyVerifiedException;
use App\Exceptions\Auth\EmailVerification\EmailNotVerifiedException;
use App\Exceptions\Auth\EmailVerification\NoPendingEmailVerificationException;
use App\Exceptions\Auth\PasswordResetException;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Throwable;

readonly class AuthService
{
    public const string VERIFY_SESSION_KEY = 'auth_verification_user_id';

    public function __construct(
        private UserService $userService,
    ) {
    }

    public function register(AuthRegisterDTO $dto): User
    {
        $user = $this->userService->create(
            $dto->name,
            $dto->email,
            Hash::make($dto->password),
        );
        $user->sendEmailVerificationNotification();
        Session::put(self::VERIFY_SESSION_KEY, $user->id);

        return $user;
    }

    public function login(AuthLoginDTO $dto): void
    {
        if (!Auth::validate(['email' => $dto->email, 'password' => $dto->password])) {
            throw new AuthenticationFailedException();
        }

        $user = User::query()->findByEmail($dto->email);

        if (!isset($user)) {
            throw new AuthenticationFailedException();
        }

        if (!$user->hasVerifiedEmail()) {
            throw new EmailNotVerifiedException();
        }

        Auth::login($user, true);
    }

    public function logout(): void
    {
        Auth::logout();
        Session::regenerate();
        Session::regenerateToken();
    }

    public function verify(): void
    {
        $user = User::findOrFail(Session::get(self::VERIFY_SESSION_KEY));

        if ($user->hasVerifiedEmail()) {
            throw new EmailAlreadyVerifiedException("Email $user->email уже подтвержден");
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        Session::forget(self::VERIFY_SESSION_KEY);
        Auth::login($user, true);
    }

    public function hasUnverifiedEmail(): bool
    {
        return Session::has(self::VERIFY_SESSION_KEY);
    }

    /* @throws Throwable */
    public function resendVerificationNotification(): void
    {
        throw_unless($this->hasUnverifiedEmail(), new NoPendingEmailVerificationException());

        $user = User::findOrFail(Session::get(self::VERIFY_SESSION_KEY));
        throw_if($user->hasVerifiedEmail(), new EmailAlreadyVerifiedException());

        $user->sendEmailVerificationNotification();
    }

    public function sendForgotPasswordNotification(AuthForgotPasswordDTO $dto): void
    {
        $status = PasswordFacade::sendResetLink([
            'email' => $dto->email
        ]);

        if ($status !== PasswordFacade::RESET_LINK_SENT) {
            throw new PasswordResetException($status, __($status));
        }
    }

    public function resetPassword(AuthResetPasswordDTO $dto): void
    {
        $status = PasswordFacade::reset(
            [
                'email' => $dto->email,
                'token' => $dto->token,
                'password' => $dto->password,
                'password_confirmation' => $dto->password_confirmation,
            ],
            static function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== PasswordFacade::PASSWORD_RESET) {
            throw new PasswordResetException($status, __($status));
        }
    }
}