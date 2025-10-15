<?php

namespace App\Services\PaymentGateway;

use App\Contracts\PaymentGateway;
use App\Enum\PaymentSource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

readonly class PaymentService
{
    public function __construct(
        private PaymentGateway $gateway,
    )
    {}

    public function createForDeposit(User $user, int $amount): Payment
    {
        return $this->gateway->createPayment($user, $amount, PaymentSource::REPLENISHMENT);
    }

    public function createForOrder(Order $order): Payment
    {
        return $this->gateway->createPayment($order->user, $order->amount - $order->user->balance, PaymentSource::ORDER, $order);
    }

    public function getPaymentUrl(Payment $payment): string
    {
        return $this->gateway->getPaymentUrl($payment);
    }
}