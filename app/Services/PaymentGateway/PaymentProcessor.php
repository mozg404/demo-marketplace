<?php

namespace App\Services\PaymentGateway;

use Throwable;
use App\Enum\TransactionType;
use App\Exceptions\Payment\PaymentAlreadyCompletedException;
use App\Exceptions\Payment\PaymentFailedException;
use App\Exceptions\Payment\PaymentCancelledException;
use App\Models\Payment;
use App\Services\Balance\BalanceService;
use App\Services\Order\OrderProcessor;
use Illuminate\Support\Facades\DB;

readonly class PaymentProcessor
{
    public function __construct(
        private OrderProcessor $orderProcessor,
        private BalanceService $balanceService,
    )
    {}

    /**
     * @throws PaymentAlreadyCompletedException|Throwable
     */
    public function process(Payment $payment): Payment
    {
        throw_if($payment->isCompleted(), new PaymentAlreadyCompletedException());
        throw_if($payment->isCancelled(), new PaymentCancelledException());
        throw_if($payment->isFailed(), new PaymentFailedException());

        return DB::transaction(function () use ($payment) {
            // Пополняем баланс пользователя из платежа
            $this->balanceService->deposit(
                user: $payment->user,
                amount: $payment->amount,
                type: TransactionType::GATEWAY_DEPOSIT,
                transactionable: $payment
            );

            // Если оплата в процессе заказа - выполняем заказ
            if ($payment->isOrderSource()) {
                $this->orderProcessor->process($payment->sourceable);
            }

            // Помечаем, как выполненный
            return $payment->markAsCompleted();
        });
    }
}