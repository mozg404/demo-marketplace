<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\Product\ProductQuery;

readonly class ProductObserver
{
    public function __construct(
        private ProductQuery $productQuery,
    ) {
    }

    public function created(Product $product): void
    {
        $this->productQuery->clearCache();
    }

    public function updated(Product $product): void
    {
        $this->productQuery->clearCache();
    }
}
