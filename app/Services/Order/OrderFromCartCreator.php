<?php

namespace App\Services\Order;

use App\Events\OrderCreatedFromCart;
use App\Models\Order;
use App\Services\Cart\CartQuery;
use LogicException;

readonly class OrderFromCartCreator
{
    public function __construct(
        private CartQuery $cartQuery,
        private OrderCreator $creator,
    ) {
    }

    public function create(int $userId): Order
    {
        if ($this->cartQuery->isEmpty()) {
            throw new LogicException('Корзина пуста');
        }

        $order = $this->creator->create($userId, $this->cartQuery->all()->toPurchasableItems());

        event(new OrderCreatedFromCart($order));

        return $order;
    }
}