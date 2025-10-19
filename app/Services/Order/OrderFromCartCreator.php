<?php

namespace App\Services\Order;

use App\Events\OrderCreatedFromCart;
use App\Models\Order;
use App\Services\Cart\CartQuery;
use App\Services\Product\DTO\PurchasableItem;
use Illuminate\Support\Collection;
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

        $collection = new Collection();

        foreach ($this->cartQuery->all()?->items ?? [] as $item) {
            $collection->add(new PurchasableItem($item->product->id, $item->quantity));
        }

        $order = $this->creator->create($userId, $collection);

        event(new OrderCreatedFromCart($order));

        return $order;
    }
}