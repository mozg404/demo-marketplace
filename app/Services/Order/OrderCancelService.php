<?php

namespace App\Services\Order;

use App\Enum\OrderStatus;
use App\Exceptions\Order\CompletedOrderCannotBeCanceledException;
use App\Exceptions\Order\OrderAlreadyCanceledException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Product\Stock\StockReserver;
use Illuminate\Support\Facades\DB;

readonly class OrderCancelService
{
    public function __construct(
        private StockReserver $stockReserver,
    ) {
    }

    public function cancelOrder(Order $order): void
    {
        if ($order->isCompleted()) {
            throw new CompletedOrderCannotBeCanceledException();
        }

        if ($order->isCancelled()) {
            throw new OrderAlreadyCanceledException();
        }

        // Меняем статус заказа на отмене
        DB::transaction(function () use ($order) {
            $order->status = OrderStatus::CANCELLED;
            $order->save();

            $order->items->each(function (OrderItem $item) {
                $this->stockReserver->unreserve($item->stockItem);
            });
        });
    }
}