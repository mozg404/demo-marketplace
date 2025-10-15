<?php

namespace App\Contracts;

use App\Enum\PaymentSource;
use App\Models\Payment;
use App\Models\User;

interface PaymentGateway
{
    /**
     * Создает платеж
     */
    public function createPayment(User $user, int $amount, PaymentSource $source, Sourceable $sourceable = null): Payment;

    /**
     * Обрабатывает колбэк от шлюза
     */
    public function validateCallback(array $data): Payment;

    /**
     * Возвращает URL для редиректа на оплату
     */
    public function getPaymentUrl(Payment $payment): string;
}