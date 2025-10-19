<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    public function __invoke(AuthService $authService): RedirectResponse
    {
        $authService->logout();

        return redirect()->route('home');
    }
}
