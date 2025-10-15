<?php

namespace App\Providers;

use App\Contracts\Cart;
use App\Contracts\PaymentGateway;
use App\Models\StockItem;
use App\Models\User;
use App\Services\Cart\SessionCart;
use App\Services\PaymentGateway\DemoPaymentGateway;
use App\Services\Seo;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Cart::class, SessionCart::class);
        $this->app->bind(PaymentGateway::class, DemoPaymentGateway::class);
        $this->app->singleton(Seo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        Gate::define('view-purchased-product', function (User $user, StockItem $stockItem) {
//            return $stockItem->orderItem()
//                ->whereHas('order', fn($q) => $q->where('user_id', $user->id))
//                ->exists();
//        });
    }
}
