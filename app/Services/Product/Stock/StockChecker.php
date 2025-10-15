<?php

namespace App\Services\Product\Stock;

use App\Exceptions\Product\NotEnoughStockException;
use App\Models\Product;

readonly class StockChecker
{
    public function __construct(
        private StockQuery $stockQuery,
    ) {
    }

    public function hasEnoughStock(Product $product, int $quantity = 1): bool
    {
        return $this->stockQuery->getAvailableCount($product) >= $quantity;
    }

    public function ensureStockAvailable(Product $product, int $quantity = 1): void
    {
        if (!$this->hasEnoughStock($product, $quantity)) {
            throw new NotEnoughStockException();
        }
    }
}