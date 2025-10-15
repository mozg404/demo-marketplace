<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\AuthenticationFailedException;
use App\Exceptions\Auth\EmailVerification\EmailNotVerifiedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginStoreRequest;
use App\Services\Auth\Authenticator;
use App\Services\Auth\WebEmailVerificator;
use App\Services\Toaster;
use App\Services\User\UserQuery;
use App\Support\SeoBuilder;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
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
        LoginStoreRequest $request,
        Authenticator $authenticator,
        WebEmailVerificator $verificator,
        UserQuery $userQuery,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $authenticator->authenticate(
                email: new Email($request->input('email')),
                password: new Password($request->input('password')),
            );
            $toaster->success('Успешная авторизация');

            return redirect()->intended(route('home'));
        } catch (AuthenticationFailedException $exception) {
            return back()
                ->withErrors(['email' => $exception->getMessage()])
                ->withInput();
        } catch (EmailNotVerifiedException $exception) {
            $toaster->error($exception->getMessage());
            $verificator->sendVerificationNotification(
                $userQuery->getByEmail($request->input('email'))
            );

            return redirect()->route('verification.notice');
        } catch (Throwable $exception) { // Ловим ЛЮБОЕ другое исключение
            Log::error('Login error', ['exception' => $exception]); // Обязательно логируем
            $toaster->error('Непредвиденная ошибка', 'Попробуйте позже');

            return back()->withInput();
        }
    }
}
