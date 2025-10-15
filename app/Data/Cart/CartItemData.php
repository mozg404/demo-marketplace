<?php

namespace App\Data\Cart;

use App\Data\Products\ProductData;
use App\Models\Product;
use App\ValueObjects\Price;
use Spatie\LaravelData\Data;

class CartItemData extends Data
{
    public Price $amount;

    public function __construct(
        public ProductData $product,
        public int $quantity = 1,
    ) {
        $this->amount = $this->product->price->multiply($this->quantity);
    }

    public static function fromMultiple(Product $product, int $quantity): self
    {
        return new self(ProductData::from($product), $quantity);
    }
}
