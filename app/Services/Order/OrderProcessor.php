<?php

namespace App\Services\Order;

use App\Exceptions\Order\OrderAlreadyProcessedException;
use App\Models\Order;
use App\Services\Balance\OrderPaymentService;
use Illuminate\Support\Facades\DB;

readonly class OrderProcessor
{
    public function __construct(
        private OrderPaymentService $paymentService,
    ) {}

    public function process(Order $order): void
    {
        throw_if(!$order->isPending(), new OrderAlreadyProcessedException());

        DB::transaction(function () use ($order) {
            $this->paymentService->processOrderPayment($order);
            $this->paymentService->processSellerPayouts($order);
            $order->markAsPaid();
        });
    }
}