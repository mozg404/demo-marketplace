<?php

namespace App\Exceptions\Balance;

use RuntimeException;

class InsufficientFundsException extends RuntimeException
{
    protected $message = 'Недостаточно средств для выполнения операции';
    protected $code = 422;
}