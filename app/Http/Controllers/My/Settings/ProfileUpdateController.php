<?php

namespace App\Http\Controllers\My\Settings;

use App\DTO\User\UserUpdateProfileDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\MySettings\ProfileUpdateRequest;
use App\Services\Toaster;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfileUpdateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('my/account/ProfileUpdatePage', [
            'name' => auth()->user()->name,
        ]);
    }

    public function update(
        UserUpdateProfileDto $dto,
        UserService $userService,
        ProfileUpdateRequest $request,
        Toaster $toaster,
    ): RedirectResponse {
        $userService->updateProfile(auth()->user(), $dto);
        $toaster->success('Профиль обновлен');

        return redirect()->back();
    }
}
