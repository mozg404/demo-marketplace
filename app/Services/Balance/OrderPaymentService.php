<?php

namespace App\Services\Balance;

use App\Enum\TransactionType;
use App\Models\Order;

readonly class OrderPaymentService
{
    public function __construct(
        private BalanceService $balanceService,
    ) {}

    public function processOrderPayment(Order $order): void
    {
        $this->balanceService->withdraw(
            user: $order->user,
            amount: $order->amount,
            type: TransactionType::ORDER_PAYMENT,
            transactionable: $order
        );
    }

    public function processSellerPayouts(Order $order): void
    {
        foreach ($order->items as $item) {
            $this->balanceService->deposit(
                user: $item->seller,
                amount: $item->current_price,
                type: TransactionType::SELLER_PAYOUT,
                transactionable: $item
            );
        }
    }
}
