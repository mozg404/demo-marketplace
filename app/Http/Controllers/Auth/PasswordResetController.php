<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\PasswordResetException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetUpdateRequest;
use App\Services\Auth\PasswordResetter;
use App\Services\Toaster;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    public function reset(Request $request, string $token): Response
    {
        return Inertia::render('auth/ResetPasswordPage', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function update(
        PasswordResetUpdateRequest $request,
        PasswordResetter $passwordResetter,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $passwordResetter->reset(
                new Email($request->input('email')),
                new Password($request->input('password')),
                new Password($request->input('password_confirmation')),
                $request->input('token'),
            );
            $toaster->success('Пароль изменен', 'Повторите попытку входа');

            return redirect()->route('login');
        } catch (PasswordResetException $e) {
            return redirect()->back()->withErrors(['email' => [$e->getMessage()]]);
        }
    }
}
