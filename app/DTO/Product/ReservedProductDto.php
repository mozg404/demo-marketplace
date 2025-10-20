<?php

namespace App\DTO\Product;

use App\Contracts\HasQuantityPrice;
use App\ValueObjects\Price;
use Spatie\LaravelData\Data;

class ReservedProductDto extends Data implements HasQuantityPrice
{
    public function __construct(
        public int $productId,
        public int $quantity,
        public Price $price,
        public array $stockIds,
    ) {
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}