<?php

namespace App\Services\Order;

use App\Collections\CreatableOrderItemCollection;
use App\Data\Orders\CreatableOrderItemData;
use App\Events\OrderCreatedFromCart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartQuery;

readonly class OrderFromCartCreator
{
    public function __construct(
        private CartQuery $cartQuery,
        private OrderCreator $creator,
    ) {
    }

    public function create(User $user): Order
    {
        $collection = new CreatableOrderItemCollection();

        foreach ($this->cartQuery->all()?->items ?? [] as $item) {
            $collection->add(new CreatableOrderItemData(
                product: Product::find($item->product->id),
                quantity: $item->quantity,
            ));
        }

        $order = $this->creator->create($user, $collection);

        event(new OrderCreatedFromCart($order));

        return $order;
    }
}