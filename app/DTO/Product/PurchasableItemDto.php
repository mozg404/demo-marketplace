<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Data;

class PurchasableItemDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $quantity,
    ) {
    }
}