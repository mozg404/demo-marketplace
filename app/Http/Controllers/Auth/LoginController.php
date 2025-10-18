<?php

namespace App\Http\Controllers\Auth;

use App\DTO\Auth\AuthLoginDTO;
use App\Exceptions\Auth\AuthenticationFailedException;
use App\Exceptions\Auth\EmailVerification\EmailNotVerifiedException;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Services\Toaster;
use App\Support\SeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class LoginController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('auth/LoginPage', [
            'seo' => new SeoBuilder('Авторизация'),
        ]);
    }

    public function store(
        AuthLoginDTO $dto,
        AuthService $authService,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $authService->login($dto);
            $toaster->success('Успешная авторизация');

            return redirect()->intended(route('home'));
        } catch (AuthenticationFailedException $exception) {
            return back()
                ->withErrors(['email' => $exception->getMessage()])
                ->withInput();
        } catch (EmailNotVerifiedException $exception) {
            $toaster->error($exception->getMessage());

            return redirect()->route('verification.notice');
        } catch (Throwable $exception) { // Ловим ЛЮБОЕ другое исключение
            Log::error('Login error', ['exception' => $exception]); // Обязательно логируем
            $toaster->error('Непредвиденная ошибка', 'Попробуйте позже');

            return back()->withInput();
        }
    }
}
