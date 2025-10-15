<?php

namespace App\Exceptions\Product;

use DomainException;

class ProductUnavailableException extends DomainException
{
    protected $message = 'Недоступно для покупки';
    protected $code = 400;
}