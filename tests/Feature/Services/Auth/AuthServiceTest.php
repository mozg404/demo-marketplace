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
use App\Services\User\UserRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
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
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->authService = new AuthService($this->userRepository);
    }

    #[Test]
    public function itRegistersUserSuccessfully(): void
    {
        Notification::fake();

        $user = User::factory()->make(['id' => 1]);
        $dto = new AuthRegisterDTO('Vasya Pupkin', 'vasyapupkin@gmail.com', 'password');

        $this->userRepository->expects($this->once())
            ->method('create')
            ->with('Vasya Pupkin', 'vasyapupkin@gmail.com', $this->isString())
            ->willReturn($user);

        $result = $this->authService->register($dto);

        $this->assertSame($user, $result);
        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
        $this->assertEquals(1, Session::get(AuthService::VERIFY_SESSION_KEY));
    }

    #[Test]
    public function itLogsInUserSuccessfully(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $dto = new AuthLoginDTO('vasyapupkin@gmail.com', 'password');

        Auth::shouldReceive('validate')
            ->once()
            ->with(['email' => 'vasyapupkin@gmail.com', 'password' => 'password'])
            ->andReturn(true);

        $this->userRepository->expects($this->once())
            ->method('getUserByEmail')
            ->with('vasyapupkin@gmail.com')
            ->willReturn($user);

        Auth::shouldReceive('login')
            ->once()
            ->with($user, true);

        $this->authService->login($dto);
    }

    #[Test]
    public function itThrowsExceptionWhenLoginCredentialsAreInvalid(): void
    {
        $dto = new AuthLoginDTO('vasyapupkin@gmail.com', 'wrong_password');

        Auth::shouldReceive('validate')
            ->once()
            ->with(['email' => 'vasyapupkin@gmail.com', 'password' => 'wrong_password'])
            ->andReturn(false);

        $this->expectException(AuthenticationFailedException::class);

        $this->authService->login($dto);
    }

    #[Test]
    public function itThrowsExceptionWhenLoginWithUnverifiedEmail(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $dto = new AuthLoginDTO('vasyapupkin@gmail.com', 'password');

        Auth::shouldReceive('validate')
            ->once()
            ->andReturn(true);

        $this->userRepository->expects($this->once())
            ->method('getUserByEmail')
            ->with('vasyapupkin@gmail.com')
            ->willReturn($user);

        $this->expectException(EmailNotVerifiedException::class);

        $this->authService->login($dto);
    }

    #[Test]
    public function itLogsOutUserSuccessfully(): void
    {
        Auth::shouldReceive('logout')->once();
        Session::shouldReceive('regenerate')->once();
        Session::shouldReceive('regenerateToken')->once();

        $this->authService->logout();
    }

    #[Test]
    public function itVerifiesEmailSuccessfully(): void
    {
        Event::fake();

        $user = User::factory()->create(['email_verified_at' => null]);
        Session::put(AuthService::VERIFY_SESSION_KEY, $user->id);

        $this->userRepository->expects($this->once())
            ->method('get')
            ->with($user->id)
            ->willReturn($user);

        Auth::shouldReceive('login')
            ->once()
            ->with($user, true);

        $this->authService->verify();

        $this->assertNotNull($user->fresh()->email_verified_at);
        Event::assertDispatched(Verified::class);
        $this->assertFalse(Session::has(AuthService::VERIFY_SESSION_KEY));
    }

    #[Test]
    public function itThrowsExceptionWhenVerifyingAlreadyVerifiedEmail(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Session::put(AuthService::VERIFY_SESSION_KEY, $user->id);

        $this->userRepository->expects($this->once())
            ->method('get')
            ->with($user->id)
            ->willReturn($user);

        $this->expectException(EmailAlreadyVerifiedException::class);

        $this->authService->verify();
    }

    #[Test]
    public function itChecksForUnverifiedEmail(): void
    {
        Session::put(AuthService::VERIFY_SESSION_KEY, 1);
        $this->assertTrue($this->authService->hasUnverifiedEmail());

        Session::forget(AuthService::VERIFY_SESSION_KEY);
        $this->assertFalse($this->authService->hasUnverifiedEmail());
    }

    #[Test]
    public function itResendsVerificationNotificationSuccessfully(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => null]);
        Session::put(AuthService::VERIFY_SESSION_KEY, $user->id);

        $this->userRepository->expects($this->once())
            ->method('get')
            ->with($user->id)
            ->willReturn($user);

        $this->authService->resendVerificationNotification();

        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    #[Test]
    public function itThrowsExceptionWhenResendingWithoutPendingVerification(): void
    {
        $this->expectException(NoPendingEmailVerificationException::class);

        $this->authService->resendVerificationNotification();
    }

    #[Test]
    public function itThrowsExceptionWhenResendingForAlreadyVerifiedEmail(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Session::put(AuthService::VERIFY_SESSION_KEY, $user->id);

        $this->userRepository->expects($this->once())
            ->method('get')
            ->with($user->id)
            ->willReturn($user);

        $this->expectException(EmailAlreadyVerifiedException::class);

        $this->authService->resendVerificationNotification();
    }

    #[Test]
    public function itSendsForgotPasswordNotificationSuccessfully(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'vasyapupkin@gmail.com'])
            ->andReturn(Password::RESET_LINK_SENT);

        $dto = new AuthForgotPasswordDTO('vasyapupkin@gmail.com');

        $this->authService->sendForgotPasswordNotification($dto);
    }

    #[Test]
    public function itThrowsExceptionWhenForgotPasswordFails(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'vasyapupkin@gmail.com'])
            ->andReturn(Password::INVALID_USER);

        $dto = new AuthForgotPasswordDTO('vasyapupkin@gmail.com');

        $this->expectException(PasswordResetException::class);

        $this->authService->sendForgotPasswordNotification($dto);
    }

    #[Test]
    public function itResetsPasswordSuccessfully(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $dto = new AuthResetPasswordDTO('vasyapupkin@gmail.com', 'token', 'new_password', 'new_password');

        Password::shouldReceive('reset')
            ->once()
            ->with([
                'email' => 'vasyapupkin@gmail.com',
                'token' => 'token', // ← исправлен порядок
                'password' => 'new_password',
                'password_confirmation' => 'new_password',
            ], $this->isCallable())
            ->andReturnUsing(function ($credentials, $callback) use ($user) {
                $callback($user, 'new_password');
                return Password::PASSWORD_RESET;
            });

        $this->authService->resetPassword($dto);

        $this->assertTrue(Hash::check('new_password', $user->fresh()->password));
        Event::assertDispatched(PasswordReset::class);
    }

    #[Test]
    public function itThrowsExceptionWhenPasswordResetFails(): void
    {
        $dto = new AuthResetPasswordDTO('vasyapupkin@gmail.com', 'token', 'new_password', 'new_password');

        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_TOKEN);

        $this->expectException(PasswordResetException::class);

        $this->authService->resetPassword($dto);
    }
}