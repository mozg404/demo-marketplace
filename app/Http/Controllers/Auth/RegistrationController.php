<?php

namespace App\Http\Controllers\Auth;

use App\DTO\Auth\AuthRegisterDTO;
use App\Exceptions\User\EmailAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Services\Toaster;
use App\Support\SeoBuilder;
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
        AuthRegisterDTO $dto,
        AuthService $authService,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $authService->register($dto);

            return redirect()->route('verification.notice');
        } catch (InvalidArgumentException|EmailAlreadyExistsException $exception) {
            $toaster->error($exception->getMessage());

            return back()->withInput();
        }
    }
}
