<?php

namespace App\Http\Controllers\My\Settings;

use App\DTO\User\UserUpdateAvatarDto;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Toaster;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class ChangeAvatarController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('common/ImageUploaderModal', [
            'imageUrl' => auth()->user()->getFirstMediaUrl(User::MEDIA_COLLECTION_AVATAR),
            'aspectRatio' => 1,
            'saveRoute' => route('my.settings.change.avatar.update'),
        ]);
    }

    public function update(
        UserUpdateAvatarDto $dto,
        UserService $userService,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $userService->updateAvatar(auth()->user(), $dto);
            $toaster->success('Аватар обновлен');

            return redirect()->back();
        } catch (FileCannotBeAdded $e) {
            $toaster->error('Не удалось загрузить аватар');

            return redirect()->back()->withErrors(['image' => 'Не удалось загрузить аватар']);
        }
    }
}
