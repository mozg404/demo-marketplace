<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\PasswordResetException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordForgotStoreRequest;
use App\Services\Auth\PasswordResetter;
use App\Services\Toaster;
use App\ValueObjects\Email;
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
        PasswordForgotStoreRequest $request,
        PasswordResetter $passwordResetter,
        Toaster $toaster
    ): RedirectResponse {
        try {
            $passwordResetter->sendResetPasswordNotification(
                new Email($request->input('email'))
            );
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
