<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Data;

class PurchasableItemDto extends Data
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity = 1,
    ) {
    }
}