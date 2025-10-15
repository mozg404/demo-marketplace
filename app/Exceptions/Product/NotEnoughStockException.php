<?php

namespace App\Exceptions\Product;

use DomainException;

class NotEnoughStockException extends DomainException
{
    protected $message = 'Недостаточно товара на складе';
    protected $code = 422;
}