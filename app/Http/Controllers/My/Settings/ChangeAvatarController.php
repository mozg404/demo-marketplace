<?php

namespace App\Http\Controllers\My\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\MySettings\ChangeAvatarRequest;
use App\Models\User;
use App\Services\Toaster;
use App\Services\User\UserAvatarChanger;
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
        ChangeAvatarRequest $request,
        UserAvatarChanger $avatarChanger,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $avatarChanger->changeFromUploadedFile(
                auth()->user(),
                $request->file('image')
            );
            $toaster->success('Аватар обновлен');

            return redirect()->back();
        } catch (FileCannotBeAdded $e) {
            $toaster->error('Не удалось загрузить аватар');

            return redirect()->back()->withErrors(['image' => 'Не удалось загрузить аватар']);
        }
    }
}
