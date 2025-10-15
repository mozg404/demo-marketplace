<?php

namespace App\Listeners;

use App\Events\OrderCreatedFromCart;
use App\Services\Cart\CartManager;

readonly class ClearCartAfterOrder
{
    public function __construct(
        private CartManager $cartManager
    ) {}

    public function handle(OrderCreatedFromCart $event): void
    {
        $this->cartManager->clear();
    }
}
