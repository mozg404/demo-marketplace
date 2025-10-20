<?php

namespace Services\Auth;

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
use App\Services\Auth\AuthService;
use App\Services\User\UserService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = app(AuthService::class);
        $this->userService = app(UserService::class);
    }

    #[Test]
    public function registerSuccessfullyCreatesUserAndSendsVerification(): void
    {
        Event::fake();
        Notification::fake();

        $dto = new AuthRegisterDTO(
            name: 'Test User',
            email: 'test@example.com',
            password: 'password123'
        );

        $user = $this->authService->register($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertEquals($user->id, Session::get(AuthService::VERIFY_SESSION_KEY));

        // Проверяем, что отправлено email уведомление
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[Test]
    public function loginSuccessfullyAuthenticatesVerifiedUser(): void
    {
        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123'),
            emailVerified: true
        );

        $dto = new AuthLoginDTO(
            email: 'test@example.com',
            password: 'password123'
        );

        $this->authService->login($dto);

        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    #[Test]
    public function loginThrowsExceptionForInvalidCredentials(): void
    {
        $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123'),
            emailVerified: true
        );

        $dto = new AuthLoginDTO(
            email: 'test@example.com',
            password: 'wrongpassword'
        );

        $this->expectException(AuthenticationFailedException::class);
        $this->authService->login($dto);
    }

    #[Test]
    public function loginThrowsExceptionForUnverifiedEmail(): void
    {
        $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123'),
            emailVerified: false
        );

        $dto = new AuthLoginDTO(
            email: 'test@example.com',
            password: 'password123'
        );

        $this->expectException(EmailNotVerifiedException::class);
        $this->authService->login($dto);
    }

    #[Test]
    public function logoutSuccessfullyLogsOutUser(): void
    {
        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123'),
            emailVerified: true
        );

        Auth::login($user);

        $this->authService->logout();

        $this->assertFalse(Auth::check());
    }

    #[Test]
    public function verifySuccessfullyVerifiesEmailAndLogsIn(): void
    {
        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123'),
            emailVerified: false
        );

        Session::put(AuthService::VERIFY_SESSION_KEY, $user->id);
        Event::fake();

        $this->authService->verify();

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertTrue(Auth::check());
        $this->assertFalse(Session::has(AuthService::VERIFY_SESSION_KEY));
        Event::assertDispatched(Verified::class);
    }

    #[Test]
    public function verifyThrowsExceptionForAlreadyVerifiedEmail(): void
    {
        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123'),
            emailVerified: true
        );

        Session::put(AuthService::VERIFY_SESSION_KEY, $user->id);

        $this->expectException(EmailAlreadyVerifiedException::class);
        $this->authService->verify();
    }

    #[Test]
    public function hasUnverifiedEmailReturnsTrueWhenSessionHasUserId(): void
    {
        Session::put(AuthService::VERIFY_SESSION_KEY, 1);

        $result = $this->authService->hasUnverifiedEmail();

        $this->assertTrue($result);
    }

    #[Test]
    public function hasUnverifiedEmailReturnsFalseWhenSessionEmpty(): void
    {
        Session::flush();

        $result = $this->authService->hasUnverifiedEmail();

        $this->assertFalse($result);
    }

    #[Test]
    public function resendVerificationNotificationSuccessfullyResends(): void
    {
        Notification::fake();

        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123'),
            emailVerified: false
        );

        Session::put(AuthService::VERIFY_SESSION_KEY, $user->id);

        $this->authService->resendVerificationNotification();

        // Проверяем, что email уведомление было отправлено повторно
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[Test]
    public function resendVerificationNotificationThrowsExceptionWhenNoPendingVerification(): void
    {
        Session::flush();

        $this->expectException(NoPendingEmailVerificationException::class);
        $this->authService->resendVerificationNotification();
    }

    #[Test]
    public function resendVerificationNotificationThrowsExceptionForAlreadyVerified(): void
    {
        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123'),
            emailVerified: true
        );

        Session::put(AuthService::VERIFY_SESSION_KEY, $user->id);

        $this->expectException(EmailAlreadyVerifiedException::class);
        $this->authService->resendVerificationNotification();
    }

    #[Test]
    public function sendForgotPasswordNotificationSuccessfullySendsResetLink(): void
    {
        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123')
        );

        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'test@example.com'])
            ->andReturn(Password::RESET_LINK_SENT);

        $dto = new AuthForgotPasswordDTO(email: 'test@example.com');

        $this->authService->sendForgotPasswordNotification($dto);

        $this->assertTrue(true); // Если не выброшено исключение - тест пройден
    }

    #[Test]
    public function sendForgotPasswordNotificationThrowsExceptionOnFailure(): void
    {
        $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('password123')
        );

        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'test@example.com'])
            ->andReturn(Password::INVALID_USER);

        $dto = new AuthForgotPasswordDTO(email: 'test@example.com');

        $this->expectException(PasswordResetException::class);
        $this->authService->sendForgotPasswordNotification($dto);
    }

    #[Test]
    public function resetPasswordSuccessfullyResetsPassword(): void
    {
        Event::fake();

        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('oldpassword')
        );

        Password::shouldReceive('reset')
            ->once()
            ->andReturnUsing(function ($credentials, $callback) use ($user) {
                $callback($user, 'newpassword');
                return Password::PASSWORD_RESET;
            });

        $dto = new AuthResetPasswordDTO(
            email: 'test@example.com',
            token: 'valid-token',
            password: 'newpassword',
            password_confirmation: 'newpassword'
        );

        $this->authService->resetPassword($dto);

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
        Event::assertDispatched(PasswordReset::class);
    }

    #[Test]
    public function resetPasswordThrowsExceptionOnFailure(): void
    {
        $user = $this->userService->create(
            'Test User',
            'test@example.com',
            Hash::make('oldpassword')
        );

        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_TOKEN);

        $dto = new AuthResetPasswordDTO(
            email: 'test@example.com',
            token: 'invalid-token',
            password: 'newpassword',
            password_confirmation: 'newpassword'
        );

        $this->expectException(PasswordResetException::class);
        $this->authService->resetPassword($dto);
    }
}