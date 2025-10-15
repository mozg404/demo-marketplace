<?php

namespace App\Exceptions\Payment;

use ErrorException;

class PaymentAlreadyCompletedException extends ErrorException
{
    protected $message = 'Платеж уже обработан';
    protected $code = 422;
}