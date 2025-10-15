<?php

use App\Http\Middleware\CheckoutRedirectAfterAuth;
use App\Http\Middleware\HandleInertiaRequests;
use App\Jobs\CreateRandomFeedback;
use App\Jobs\CreateRandomOrder;
use App\Jobs\CreateRandomProduct;
use App\Jobs\CreateRandomUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            CheckoutRedirectAfterAuth::class,
            HandleInertiaRequests::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(new CreateRandomUser())->everyThreeHours();
        $schedule->job(new CreateRandomProduct())->hourly();
        $schedule->job(new CreateRandomOrder())->everyThirtyMinutes();
        $schedule->job(new CreateRandomFeedback())->everyFifteenMinutes();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
