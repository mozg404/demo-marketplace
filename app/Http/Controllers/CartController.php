<?php

namespace App\Http\Controllers;

use App\Exceptions\Product\NotEnoughStockException;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Product;
use App\Services\Cart\CartManager;
use App\Services\Cart\CartQuery;
use App\Services\Cart\CartValidator;
use App\Services\Toaster;
use App\Support\SeoBuilder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(
        private readonly CartManager $cartManager,
        private readonly Toaster $toaster,
    ) {
    }

    public function index(CartQuery $cartQuery): Response
    {
        $props = ['seo' => new SeoBuilder('Корзина')];

        if ($cartQuery->isEmpty()) {
            return Inertia::render('cart/EmptyCartIndexPage', $props);
        }

        return Inertia::render('cart/CartIndexPage', $props);
    }

    public function store(
        Product $product,
        CartValidator $cartValidator
    ): RedirectResponse {
        try {
            $cartValidator->validateAdd($product);
            $this->cartManager->add($product);
            $this->toaster->success('Добавлено в корзину');

            return back();
        } catch (ProductUnavailableException $e) {
            $this->toaster->error($e->getMessage());

            return back();
        }
    }

    public function decrease(Product $product): RedirectResponse
    {
        $this->cartManager->remove($product);
        $this->toaster->info('Позиция убрана из корзины');

        return back();
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->cartManager->removeItem($product);
        $this->toaster->info('Товар удален из корзины');

        return back();
    }

    public function clear(): RedirectResponse
    {
        $this->cartManager->clear();

        return back();
    }
}
