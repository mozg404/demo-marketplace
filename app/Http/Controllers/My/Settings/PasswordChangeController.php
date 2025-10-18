<?php

namespace App\Http\Controllers\My\Settings;

use App\DTO\User\UserUpdatePasswordDto;
use App\Exceptions\Auth\InvalidPasswordException;
use App\Http\Controllers\Controller;
use App\Services\Toaster;
use App\Services\User\UserService;
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
        UserUpdatePasswordDto $dto,
        UserService $service,
        Toaster $toaster,
    ): RedirectResponse
    {
        try {
            $service->updatePassword(auth()->user(), $dto);
            $toaster->success('Пароль успешно изменен');

            return redirect()->back();
        } catch (InvalidPasswordException $exception) {
            return redirect()->back()->WithErrors(['old_password' => [$exception->getMessage()]]);
        }
    }
}
