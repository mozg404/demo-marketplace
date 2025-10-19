<?php

namespace App\Services\Cart;

use App\Contracts\Cart;
use App\DTO\Cart\CartDto;
use App\DTO\Cart\CartItemDto;
use App\Models\Product;

readonly class CartQuery
{
    public function __construct(
        private Cart $cart,
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

    public function all(): CartDto
    {
        $items = [];

        foreach ($this->cart->getItems() as $id => $quantity) {
            $items[] = new CartItemDto($id, $quantity);
        }

        return CartDto::from($items);
    }
}