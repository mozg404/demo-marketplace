<?php

namespace App\Services\Cart;

use App\Contracts\Cart;
use App\Models\Product;

readonly class CartManager
{
    public function __construct(
        private Cart $cart,
    ) {
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $this->cart->add($product->id, $quantity);
    }

    public function remove(Product $product, int $quantity = 1): void
    {
        $this->cart->remove($product->id, $quantity);
    }

    public function removeItem(Product $product): void
    {
        $this->cart->removeItem($product->id);
    }

    public function clear(): void
    {
        $this->cart->clear();
    }
}