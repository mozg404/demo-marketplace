<?php

namespace App\Exceptions\Order;

use DomainException;

class OrderAccessDeniedException extends DomainException
{
    protected $message = 'Заказ не принадлежит пользователю';
    protected $code = 403;
}