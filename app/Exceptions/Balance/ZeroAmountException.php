<?php

namespace App\Exceptions\Balance;

use DomainException;

class ZeroAmountException extends DomainException
{
    protected $message = 'Сумма не может быть нулевой';
    protected $code = 422;
}