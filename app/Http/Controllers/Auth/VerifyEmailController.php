<?php

namespace App\Http\Controllers\Auth;

use App\Services\User\UserQuery;
use Exception;
use App\Exceptions\Auth\EmailVerification\NoPendingEmailVerificationException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\Authenticator;
use App\Services\Auth\WebEmailVerificator;
use App\Services\Toaster;
use App\Support\SeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifyEmailController extends Controller
{
    public function __construct(
        private readonly Toaster $toaster,
        private readonly WebEmailVerificator $emailVerificator,
        private readonly Authenticator $authorizer,
        private readonly UserQuery $userQuery,
    ) {
    }

    public function notice(Request $request): Response|RedirectResponse
    {
        try {
            $user = $this->emailVerificator->getVerificationUser();

            return Inertia::render('auth/EmailVerifyExpectationPage', [
                'user' => $user,
                'seo' => new SeoBuilder('Подтвердите Email'),
            ]);
        } catch (NoPendingEmailVerificationException $exception) {
            $this->toaster->error($exception->getMessage(), 'Войдите в аккаунт');

            return redirect()->route('login');
        } catch (Exception $exception) {
            $this->toaster->error('Ошибка', 'Войдите в аккаунт');

            return redirect()->route('login');
        }
    }

    public function verify(int $id, string $hash): RedirectResponse
    {
        try {
            $user = $this->userQuery->get($id);

            // Верификация мыла
            $this->emailVerificator->verify($user, $hash);

            // Логика авторизации
            $this->authorizer->login($user);

            // Уведомление об успешной верификации
            $this->toaster->success('Успешная авторизация');

            // Редирект на главную
            return redirect()->route('home');
        } catch (Exception $exception) {
            $this->toaster->error($exception->getMessage());

            return redirect()->route('login');
        }
    }

    public function resend(): RedirectResponse
    {
        $this->emailVerificator->resendVerificationNotification();
        $this->toaster->success('Письмо отправлено заново', 'Проверьте почту');

        return redirect()->route('verification.notice');
    }
}
