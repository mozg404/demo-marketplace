<?php

namespace App\Services\Product\DTO;

use Spatie\LaravelData\Data;

class PurchasableItem extends Data
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity = 1,
    ) {
    }
}