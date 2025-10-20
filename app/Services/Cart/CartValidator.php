<?php

namespace App\Services\Cart;

use App\DTO\Product\PurchasableItemDto;
use App\Models\Product;
use App\Services\Product\ProductSaleManager;

readonly class CartValidator
{
    public function __construct(
        private ProductSaleManager $saleManager,
        private CartQuery $cartQuery,
    ) {
    }

    public function validateAdd(Product $product, int $quantity = 1): void
    {
        $this->saleManager->validatePurchasableItems([
            new PurchasableItemDto($product->id, $quantity + $this->cartQuery->getQuantityFor($product))
        ]);
    }
}