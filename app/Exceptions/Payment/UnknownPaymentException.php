<?php

namespace App\Exceptions\Payment;

use ErrorException;

class UnknownPaymentException extends ErrorException
{
    protected $message = 'Неизвестный платеж';
    protected $code = 404;
}