<?php

namespace App\Services\Demo;

use App\DTO\Product\PurchasableItemDto;
use App\Enum\TransactionType;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Balance\BalanceService;
use App\Services\Order\OrderCreator;
use App\Services\Order\OrderProcessor;
use Illuminate\Support\Carbon;

readonly class DemoOrderCreator
{
    public function __construct(
        private OrderCreator $orderCreator,
        private OrderProcessor $orderProcessor,
        private BalanceService $balanceService,
    ) {
    }

    public function create(User $user): Order
    {
        $purchasableProducts = Product::query()
            ->isAvailable()
            ->whereNotBelongsToUser($user)
            ->take(random_int(config('demo.min_order_random_items'), config('demo.max_order_random_items')))
            ->get()
            ->map(fn(Product $product) => new PurchasableItemDto($product->id, 1));

        $order = $this->orderCreator->create($user->id, $purchasableProducts);
        $order->update(['created_at' => new Carbon(fake()->dateTimeBetween('-1 year'))]);

        return $order;

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