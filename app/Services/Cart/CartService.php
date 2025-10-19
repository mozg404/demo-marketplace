<?php

namespace App\Services\Cart;

use App\Contracts\Cart;
use App\Data\Cart\CartData;
use App\Data\Cart\CartItemData;
use App\Models\Product;

readonly class CartService
{
    public function __construct(
        private Cart $cart,
    ) {
    }

    public function all(): CartData
    {
        $products = Product::query()
            ->whereIds($this->cart->getIds())
            ->withAvailableCount()
            ->get()
            ?->map(function (Product $product) {
                return CartItemData::from($product, $this->cart->getQuantityFor($product->id));
            });



        return new CartData($products ?? []);
    }
}