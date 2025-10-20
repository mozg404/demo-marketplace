<?php

namespace App\DTO\Cart;

use Spatie\LaravelData\Data;

class CartItemDto extends Data
{
    public function __construct(
        readonly public int $id,
        readonly public int $quantity,
    ) {
    }
}