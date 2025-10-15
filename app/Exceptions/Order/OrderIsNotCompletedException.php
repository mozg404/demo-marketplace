<?php

namespace App\Exceptions\Order;

use DomainException;

class OrderIsNotCompletedException extends DomainException
{
    protected $message = 'Заказ не завершен';
    protected $code = 409;
}