<?php

namespace App\Services\Product;

use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Product;
use App\Services\Product\Stock\StockChecker;

readonly class ProductAvailabilityChecker
{
    public function __construct(
        private StockChecker $stockChecker,
    ) {
    }

    public function ensureProductIsActive(Product $product): void
    {
        if (!$product->isActive()) {
            throw new ProductUnavailableException();
        }
    }

    public function ensureCanByPurchased(Product $product, int $quantity = 1): void
    {
        $this->ensureProductIsActive($product);
        $this->stockChecker->ensureStockAvailable($product, $quantity);
    }
}