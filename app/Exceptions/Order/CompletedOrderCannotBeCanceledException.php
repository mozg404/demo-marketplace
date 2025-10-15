<?php

namespace App\Exceptions\Order;

use DomainException;

class CompletedOrderCannotBeCanceledException extends DomainException
{
    protected $message = 'Выполненный заказ нельзя отменить';
    protected $code = 409;
}