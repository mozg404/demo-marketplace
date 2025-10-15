<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\User\EmailAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationStoreRequest;
use App\Services\Auth\WebEmailVerificator;
use App\Services\Toaster;
use App\Services\User\UserRegistrar;
use App\Support\SeoBuilder;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class RegistrationController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('auth/RegistrationPage', [
            'seo' => new SeoBuilder('Регистрация'),
        ]);
    }

    public function store(
        RegistrationStoreRequest $request,
        UserRegistrar $userRegistrar,
        WebEmailVerificator $emailVerificator,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            // Регистрируем аккаунт
            $user = $userRegistrar->register(
                $request->input('name'),
                new Email($request->input('email')),
                new Password($request->input('password'))
            );

            // Отправляем письмо для подтверждения почты
            $emailVerificator->sendVerificationNotification($user);

            // Редирект на сообщение об отправленном подтверждении
            return redirect()->route('verification.notice');
        } catch (InvalidArgumentException|EmailAlreadyExistsException $exception) {
            $toaster->error($exception->getMessage());

            return back()->withInput();
        }
    }
}
