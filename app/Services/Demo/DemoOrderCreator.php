<?php

namespace App\Services\Demo;

use App\Collections\CreatableOrderItemCollection;
use App\Enum\TransactionType;
use App\Models\Order;
use App\Models\User;
use App\Services\Balance\BalanceService;
use App\Services\Order\OrderCreator;
use App\Services\Order\OrderProcessor;
use App\Services\Product\ProductQuery;
use Illuminate\Support\Carbon;

readonly class DemoOrderCreator
{
    public function __construct(
        private ProductQuery $productQuery,
        private OrderCreator $orderCreator,
        private OrderProcessor $orderProcessor,
        private BalanceService $balanceService,
    ) {
    }

    public function create(User $user): Order
    {
        $products = $this->productQuery->query()
            ->isAvailable()
            ->whereNotBelongsToUser($user)
            ->take(random_int(config('demo.min_order_random_items'), config('demo.max_order_random_items')))
            ->get();

        return $this->orderCreator->create(
            user: $user,
            items: CreatableOrderItemCollection::fromProductCollection($products),
            createdAt: new Carbon(fake()->dateTimeBetween('-1 year'))
        );
    }

    public function complete(Order $order): void
    {
        $this->balanceService->deposit($order->user, $order->amount, TransactionType::GATEWAY_DEPOSIT);
        $this->orderProcessor->process($order);
    }

    public function createAndComplete(User $user): Order
    {
        $order = $this->create($user);
        $this->complete($order);

        return $order;
    }
}