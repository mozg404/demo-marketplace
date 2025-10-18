<?php

namespace App\Services\Cart;

use App\Models\Product;
use App\Services\Stock\StockService;

readonly class CartValidator
{
    public function __construct(
        private CartQuery $cartQuery,
        private StockService $stockService,
    ) {
    }

    public function validateAdd(Product $product, int $quantity = 1): void
    {
        $this->stockService->ensureStockAvailable($product, $this->cartQuery->getQuantityFor($product) + $quantity);
    }
}