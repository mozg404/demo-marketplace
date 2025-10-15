<?php

namespace App\Jobs;

use App\Services\Demo\DemoFeedbackCreator;
use App\Services\Order\OrderItemQuery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class CreateRandomFeedback implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(OrderItemQuery $orderItemQuery, DemoFeedbackCreator $creator): void
    {
        $orderItem = $orderItemQuery->query()
            ->isCompleted()
            ->doesntHaveFeedback()
            ->inRandomOrder()
            ->first();

        if (isset($orderItem)) {
            $creator->create($orderItem->buyer, $orderItem);
        }
    }
}
