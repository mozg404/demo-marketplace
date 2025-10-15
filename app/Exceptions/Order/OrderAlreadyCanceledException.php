<?php

namespace App\Exceptions\Order;

use DomainException;

class OrderAlreadyCanceledException extends DomainException
{
    protected $message = 'Заказ уже отменён';
    protected $code = 409;
}