<?php

namespace App\Services\Order;

use App\Enum\TransactionType;
use App\Exceptions\Order\OrderAlreadyProcessedException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Balance\BalanceService;
use Illuminate\Support\Facades\DB;

readonly class OrderProcessor
{
    public function __construct(
        private BalanceService $balanceService,
    ) {}

    public function process(Order $order): void
    {
        throw_if(!$order->isPending(), new OrderAlreadyProcessedException());
        $this->balanceService->ensureSufficientFunds($order->user, $order->amount);

        DB::transaction(function () use ($order) {
            // Списываем со счета пользователя сумму заказа
            $this->balanceService->withdraw(
                user: $order->user,
                amount: $order->amount,
                type: TransactionType::ORDER_PAYMENT,
                transactionable: $order
            );

            // Меняем статус позиций на складе и пополняем баланс продавца
            $order->items()
                ->withStockItem()
                ->withSeller()
                ->get()->each(function (OrderItem $item) use ($order) {
                    // Выплачиваем продавцу средства
                    $this->balanceService->deposit(
                        user: $item->stockItem->product->user,
                        amount: $item->price->getCurrentPrice(),
                        type: TransactionType::SELLER_PAYOUT,
                        transactionable: $item
                    );
                });

            // Меняем статус заказа
            $order->markAsPaid();
        });
    }
}