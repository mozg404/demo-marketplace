<?php

namespace App\Data\Orders;

use App\Models\Product;
use Spatie\LaravelData\Data;

class CreatableOrderItemData extends Data
{
    public function __construct(
        public Product $product,
        public int $quantity = 1,
    ) {
    }

    public static function fromModel(Product $product): self
    {
        return new self($product);
    }
}