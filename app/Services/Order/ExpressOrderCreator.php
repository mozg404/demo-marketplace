<?php

namespace App\Services\Order;

use App\Collections\CreatableOrderItemCollection;
use App\Data\Orders\CreatableOrderItemData;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

readonly class ExpressOrderCreator
{
    public function __construct(
        private OrderCreator $creator
    ) {
    }

    public function create(User $user, Product $product): Order
    {
        return $this->creator->create($user, new CreatableOrderItemCollection([
            new CreatableOrderItemData($product)
        ]));
    }
}