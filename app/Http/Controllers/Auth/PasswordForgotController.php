<?php

namespace App\Http\Controllers\Auth;

use App\DTO\Auth\AuthForgotPasswordDTO;
use App\Exceptions\Auth\PasswordResetException;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PasswordForgotController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('auth/ForgotPasswordPage');
    }

    public function store(
        AuthForgotPasswordDTO $authForgotPasswordDTO,
        AuthService $authService,
        Toaster $toaster
    ): RedirectResponse {
        try {
            $authService->sendForgotPasswordNotification($authForgotPasswordDTO);
            $toaster->info('Проверьте почту');

            return redirect()->route('password.forgot.notify');
        } catch (PasswordResetException $e) {
            return redirect()->back()->withErrors(['email' => [$e->getMessage()]]);
        }
    }

    public function notify(): Response
    {
        return Inertia::render('auth/ForgotPasswordNotifyPage');
    }
}
