<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordForgotController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\My\Product\ProductChangeBaseController;
use App\Http\Controllers\My\Settings\ChangeAvatarController;
use App\Http\Controllers\My\Settings\PasswordChangeController;
use App\Http\Controllers\My\MyBalanceController;
use App\Http\Controllers\My\MyOrderController;
use App\Http\Controllers\My\MyPurchaseController;
use App\Http\Controllers\My\MySaleController;
use App\Http\Controllers\My\Order\OrderItemFeedbackController;
use App\Http\Controllers\My\Product\ProductChangeCategoryController;
use App\Http\Controllers\My\Product\ProductChangeDescriptionController;
use App\Http\Controllers\My\Product\ProductChangeFeaturesController;
use App\Http\Controllers\My\Product\ProductChangeImageController;
use App\Http\Controllers\My\Product\ProductChangeInstructionController;
use App\Http\Controllers\My\Product\ProductChangeNameController;
use App\Http\Controllers\My\Product\ProductChangePriceController;
use App\Http\Controllers\My\Product\ProductCreateController;
use App\Http\Controllers\My\Product\ProductEditController;
use App\Http\Controllers\My\Product\ProductIndexController as CabinetProductController;
use App\Http\Controllers\My\Product\StockController;
use App\Http\Controllers\My\Settings\ProfileUpdateController;
use App\Http\Controllers\My\SettingsController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\SandboxController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckoutAccess;
use App\Http\Middleware\CheckoutExpressAccess;
use Illuminate\Support\Facades\Route;

if (config('app.env') === 'local') {
    Route::get('/test', [TestController::class, 'test']);
    Route::get('/test-page', [TestController::class, 'testPage']);
}

// ---------------------------------------------
// My
// ---------------------------------------------

Route::middleware('auth')->prefix('/my')->group(function () {

    // ---------------------------------------------
    // Мои товары
    // ---------------------------------------------

    Route::prefix('/products')->group(function () {
        Route::get('/', [CabinetProductController::class, 'index'])->name('my.products');

        Route::get('/create', [ProductCreateController::class, 'index'])->name('my.products.create');
        Route::post('/create', [ProductCreateController::class, 'store'])->name('my.products.create.store');

        Route::middleware(['can:update,product'])->prefix('/{product}')->group(function () {
            Route::get('/edit', [ProductEditController::class, 'index'])->name('my.products.edit');

            Route::prefix('/change')->group(function () {
                Route::get('/base', [ProductChangeBaseController::class, 'index'])->name('my.products.change.base');
                Route::patch('/base', [ProductChangeBaseController::class, 'update'])->name('my.products.change.base.update');

                Route::get('/image', [ProductChangeImageController::class, 'index'])->name('my.products.change.image');
                Route::patch('/image', [ProductChangeImageController::class, 'update'])->name('my.products.change.image.update');

                Route::get('/features', [ProductChangeFeaturesController::class, 'index'])->name('my.products.change.features');
                Route::patch('/features', [ProductChangeFeaturesController::class, 'update'])->name('my.products.change.features.update');

                Route::get('/description', [ProductChangeDescriptionController::class, 'index'])->name('my.products.change.description');
                Route::patch('/description', [ProductChangeDescriptionController::class, 'update'])->name('my.products.change.description.update');

                Route::get('/instruction', [ProductChangeInstructionController::class, 'index'])->name('my.products.change.instruction');
                Route::patch('/instruction', [ProductChangeInstructionController::class, 'update'])->name('my.products.change.instruction.update');
            });

            Route::prefix('/stock')->group(function () {
                Route::get('/', [StockController::class, 'index'])->name('my.products.stock');
                Route::get('/create', [StockController::class, 'create'])->name('my.products.stock.create');
                Route::post('/', [StockController::class, 'store'])->name('my.products.stock.store');
                Route::get('/{stock_item}/edit', [StockController::class, 'edit'])->name('my.products.stock.edit');
                Route::put('/{stock_item}', [StockController::class, 'update'])->name('my.products.stock.update');
            });
        });
    });

    // ---------------------------------------------
    // Настройки профиля
    // ---------------------------------------------

    Route::prefix('/settings')->group(function () {
        // Изменение профиля
        Route::get('/', [ProfileUpdateController::class, 'index'])->name('my.settings');
        Route::patch('/', [ProfileUpdateController::class, 'update'])->name('my.settings.change.profile.update');
        // Изменение пароля
        Route::get('/change-password', [PasswordChangeController::class, 'index'])->name('my.settings.change.password');
        Route::patch('/change-password', [PasswordChangeController::class, 'update'])->name('my.settings.change.password.update');
        // Изменение аватара
        Route::get('/change-avatar', [ChangeAvatarController::class, 'index'])->name('my.settings.change.avatar');
        Route::patch('/change-avatar', [ChangeAvatarController::class, 'update'])->name('my.settings.change.avatar.update');
    });

    // ---------------------------------------------
    // Мои заказы
    // ---------------------------------------------

    Route::prefix('/orders')->group(function () {

        Route::prefix('/item/{order_item}/feedback')->group(function () {
            Route::get('/create', [OrderItemFeedbackController::class, 'create'])->name('my.orders.item.feedback.create');
            Route::post('/', [OrderItemFeedbackController::class, 'store'])->name('my.orders.item.feedback.store');
            Route::get('/{feedback}/edit', [OrderItemFeedbackController::class, 'edit'])->name('my.orders.item.feedback.edit');
            Route::put('/{feedback}', [OrderItemFeedbackController::class, 'update'])->name('my.orders.item.feedback.update');
            Route::delete('/{feedback}', [OrderItemFeedbackController::class, 'destroy'])->name('my.orders.item.feedback.destroy');
        });

        // Заказы
        Route::get('/{order}/cancel', [MyOrderController::class, 'cancel'])->can('cancel', 'order')->name('my.orders.cancel');
        Route::get('/{order}/pay', [MyOrderController::class, 'pay'])->can('pay', 'order')->name('my.orders.pay');
        Route::get('/{order}', [MyOrderController::class, 'show'])->can('view', 'order')->name('my.orders.show');
        Route::get('/', [MyOrderController::class, 'index'])->name('my.orders');
    });

    // ---------------------------------------------
    // Мой баланс
    // ---------------------------------------------

    Route::get('/balance', [MyBalanceController::class, 'index'])->name('my.balance');
    Route::post('/balance/deposit', [MyBalanceController::class, 'deposit'])->name('my.balance.deposit');

    // ---------------------------------------------
    // Мои покупки
    // ---------------------------------------------

    Route::get('/purchases', [MyPurchaseController::class, 'index'])->name('my.purchases');
    Route::get('/purchases/{order_item}', [MyPurchaseController::class, 'content'])
        ->can('view', 'order_item')
        ->name('my.purchases.content');

    // ---------------------------------------------
    // Мои продажи
    // ---------------------------------------------

    Route::get('/sales', [MySaleController::class, 'index'])->name('my.sales');

});

// ---------------------------------------------
// Корзина
// ---------------------------------------------

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/items/{product}', [CartController::class, 'store'])->name('cart.items.store');
Route::post('/cart/items/{product}/decrease', [CartController::class, 'decrease'])->name('cart.items.decrease');
Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');

// ---------------------------------------------
// Оформление заказа
// ---------------------------------------------

Route::get('/checkout', [CheckoutController::class, 'cart'])
    ->middleware(CheckoutAccess::class)
    ->name('checkout');
Route::get('/checkout/express/{product}', [CheckoutController::class, 'express'])
    ->middleware(CheckoutExpressAccess::class)
    ->name('checkout.express');

// ---------------------------------------------
// Аккаунты пользователей
// ---------------------------------------------

Route::get('/users', [UserController::class, 'index'])->name('users');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

// ---------------------------------------------
// Каталог
// ---------------------------------------------

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/product/{product}', [CatalogController::class, 'show'])->name('catalog.product');
Route::get('/catalog/{category:full_path}', [CatalogController::class, 'category'])->where('category', '.*')->name('catalog.category');

// ---------------------------------------------
// Эмуляция кассы
// ---------------------------------------------

Route::get('/demo/sandbox/{hash}', [SandboxController::class, 'index'])->name('sandbox');
Route::get('/demo/sandbox/{hash}/failed', [SandboxController::class, 'failed'])->name('sandbox.failed');
Route::get('/demo/sandbox/{hash}/success', [SandboxController::class, 'success'])->name('sandbox.success');
Route::get('/demo/sandbox/{hash}/cancelled', [SandboxController::class, 'cancelled'])->name('sandbox.cancelled');

// ---------------------------------------------
// Обработка внешнего платежа
// ---------------------------------------------

Route::get('/payment/callback', PaymentCallbackController::class)->name('payment.callback');

// ---------------------------------------------
// Авторизация
// ---------------------------------------------

Route::middleware('guest')->group(function () {
    Route::post('/registration/store', [RegistrationController::class, 'store'])->name('registration.store');
    Route::get('/registration', [RegistrationController::class, 'show'])->name('registration');

    Route::post('/login/store', [LoginController::class, 'store'])->name('login.store');
    Route::get('/login', [LoginController::class, 'show'])->name('login');

    // Верификация Email
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/resend', [VerifyEmailController::class, 'resend'])
        ->middleware(['throttle:4,1'])
        ->name('verification.resend');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Сброс пароля
    Route::get('forgot-password', [PasswordForgotController::class, 'form'])->name('password.forgot');
    Route::post('forgot-password', [PasswordForgotController::class, 'store'])->name('password.forgot.store');
    Route::get('forgot-password/notify', [PasswordForgotController::class, 'notify'])->name('password.forgot.notify');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});
Route::get('/logout', LogoutController::class)->middleware('auth')->name('logout');

// ---------------------------------------------
// Поиск
// ---------------------------------------------

Route::get('search', [SearchController::class, 'form'])->name('search');
Route::post('search', [SearchController::class, 'store'])->name('search.store');

// ---------------------------------------------
// Главная
// ---------------------------------------------

Route::get('/', IndexController::class)->name('home');
