<?php

namespace App\Services\Cart;

use App\Contracts\Cart;
use App\Data\Cart\CartData;
use App\Data\Cart\CartItemData;
use App\Models\Product;
use App\Services\Product\ProductQuery;

readonly class CartQuery
{
    public function __construct(
        private Cart $cart,
        private ProductQuery $productQuery,
    ) {
    }

    public function has(Product $product): bool
    {
        return $this->cart->has($product->id);
    }

    public function isEmpty(): bool
    {
        return $this->cart->isEmpty();
    }

    public function getQuantityFor(Product $product): int
    {
        return $this->cart->getQuantityFor($product->id);
    }

    public function all(): CartData
    {
        $items = $this->productQuery->query()
            ->whereIds($this->cart->getIds())
            ->withAvailableStockItemsCount()
            ->get()
            ?->map(function (Product $product) {
                return CartItemData::from($product, $this->getQuantityFor($product));
            });

        return new CartData($items ?? []);
    }
}