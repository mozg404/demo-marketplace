<?php

namespace App\Http\Controllers\Auth;

use App\Services\Auth\AuthService;
use Exception;
use App\Http\Controllers\Controller;
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
        private readonly AuthService $authService,
    ) {
    }

    public function notice(Request $request): Response|RedirectResponse
    {
        if ($this->authService->hasUnverifiedEmail()) {
            return Inertia::render('auth/EmailVerifyExpectationPage', [
                'seo' => new SeoBuilder('Подтвердите Email'),
            ]);
        }

        return redirect()->route('login');
    }

    public function verify(int $id, string $hash): RedirectResponse
    {
        try {
            $this->authService->verify();
            $this->toaster->success('Успешная авторизация');

            return redirect()->route('home');
        } catch (Exception $exception) {
            $this->toaster->error($exception->getMessage());

            return redirect()->route('login');
        }
    }

    public function resend(): RedirectResponse
    {
        $this->authService->resendVerificationNotification();
        $this->toaster->success('Письмо отправлено заново', 'Проверьте почту');

        return redirect()->route('verification.notice');
    }
}
