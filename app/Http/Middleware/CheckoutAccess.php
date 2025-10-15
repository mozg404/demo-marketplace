<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Проверяет авторизован ли пользователь перед оформлением заказа
 * Если не авторизован - сохраняет в сессию пометку и перенаправляет на авторизацию
 */
class CheckoutAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            $request->session()->put(CheckoutRedirectAfterAuth::SESSION_KEY, [
                'type' => 'base',
            ]);

            return redirect()->route('login');
        }

        return $next($request);
    }
}
