<?php

namespace App\Services\Cart;

use App\Models\Product;
use App\Services\Product\Stock\StockChecker;

readonly class CartValidator
{
    public function __construct(
        private CartQuery $cartQuery,
        private StockChecker $stockChecker,
    ) {
    }

    public function validateAdd(Product $product, int $quantity = 1): void
    {
        $this->stockChecker->ensureStockAvailable($product, $this->cartQuery->getQuantityFor($product) + $quantity);
    }
}