<?php

namespace App\Contracts;

use App\ValueObjects\Price;

interface HasQuantityPrice
{
    public function getPrice(): Price;
    public function getQuantity(): int;
}