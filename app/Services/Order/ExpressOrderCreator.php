<?php

namespace App\Services\Order;

use App\Collections\CreatableOrderItemCollection;
use App\Data\Orders\CreatableOrderItemData;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Product\ProductAvailabilityChecker;
use App\Services\Product\ProductService;

readonly class ExpressOrderCreator
{
    public function __construct(
        private ProductAvailabilityChecker $availabilityChecker,
        private OrderCreator $creator
    ) {
    }

    public function create(User $user, Product $product): Order
    {
        $this->availabilityChecker->ensureCanByPurchased($product);

        return $this->creator->create($user, new CreatableOrderItemCollection([
            new CreatableOrderItemData($product)
        ]));
    }
}