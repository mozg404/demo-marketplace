<?php

namespace App\Exceptions\Payment;

use Exception;

class PaymentFailedException extends Exception
{
    protected $message = 'Платеж был завершен с ошибкой';
}