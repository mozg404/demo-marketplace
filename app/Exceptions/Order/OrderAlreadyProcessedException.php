<?php

namespace App\Exceptions\Order;

use DomainException;

class OrderAlreadyProcessedException extends DomainException
{
    protected $message = 'Заказ уже был обработан';
    protected $code = 409;
}