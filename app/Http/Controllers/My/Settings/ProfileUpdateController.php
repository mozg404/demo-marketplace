<?php

namespace App\Http\Controllers\My\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\MySettings\ProfileUpdateRequest;
use App\Services\Toaster;
use App\Services\User\UserProfileUpdater;
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
        ProfileUpdateRequest $request,
        UserProfileUpdater $userService,
        Toaster $toaster,
    ): RedirectResponse {
        $userService->updateProfile(auth()->user(), $request->input('name'));
        $toaster->success('Профиль обновлен');

        return redirect()->back()->with('toasts', $toaster->all());
    }
}
