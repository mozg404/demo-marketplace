<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\Authenticator;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    public function __invoke(Authenticator $authorizer): RedirectResponse
    {
        $authorizer->logout();

        return redirect()->route('home');
    }
}
