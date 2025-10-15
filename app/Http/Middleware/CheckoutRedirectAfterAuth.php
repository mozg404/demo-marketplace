<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Если пользователь авторизован и в сессии есть ключ self::SESSION_KEY
 * удаляет ключ из сессии и перенаправляет на продолжение оформления заказа
 */
class CheckoutRedirectAfterAuth
{
    public const SESSION_KEY = 'checkout_process';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && $request->session()->has(self::SESSION_KEY)) {
            $data = $request->session()->get(self::SESSION_KEY);
            $request->session()->forget(self::SESSION_KEY);

            if ($data['type'] === 'express') {
                return redirect(route('checkout.express', $data['id']));
            }

            return redirect(route('checkout'));
        }

        return $response;
    }
}
