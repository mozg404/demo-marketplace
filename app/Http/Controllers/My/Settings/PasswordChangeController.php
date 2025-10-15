<?php

namespace App\Http\Controllers\My\Settings;

use App\Exceptions\Auth\InvalidPasswordException;
use App\Http\Controllers\Controller;
use App\Http\Requests\MySettings\PasswordChangeRequest;
use App\Services\Toaster;
use App\Services\User\UserPasswordChanger;
use App\ValueObjects\Password;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PasswordChangeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('my/account/ChangePasswordPage');
    }

    public function update(
        PasswordChangeRequest $request,
        UserPasswordChanger $passwordChanger,
        Toaster $toaster,
    ): RedirectResponse
    {
        try {
            $passwordChanger->changePassword(
                user: auth()->user(),
                oldPassword: new Password($request->input('old_password')),
                newPassword: new Password($request->input('password')),
            );
            $toaster->success('Пароль успешно изменен');

            return redirect()->back();
        } catch (InvalidPasswordException $exception) {
            return redirect()->back()->WithErrors(['old_password' => [$exception->getMessage()]]);
        }
    }
}
