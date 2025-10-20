<?php

namespace App\Http\Controllers\Auth;

use App\DTO\Auth\AuthResetPasswordDTO;
use App\Exceptions\Auth\PasswordResetException;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Services\Toaster;
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
        AuthResetPasswordDTO $dto,
        AuthService $authService,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $authService->resetPassword($dto);
            $toaster->success('Пароль изменен', 'Повторите попытку входа');

            return redirect()->route('login');
        } catch (PasswordResetException $e) {
            return redirect()->back()->withErrors(['email' => [$e->getMessage()]]);
        }
    }
}
