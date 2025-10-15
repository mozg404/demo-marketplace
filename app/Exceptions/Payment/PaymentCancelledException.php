<?php

namespace App\Exceptions\Payment;

use ErrorException;

class PaymentCancelledException extends ErrorException
{
    protected $message = 'Платеж был отменен';
}