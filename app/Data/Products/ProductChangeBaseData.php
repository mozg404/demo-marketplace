<?php

namespace App\Data\Products;

use App\Enum\ProductStatus;
use App\ValueObjects\Price;
use Spatie\LaravelData\Data;

class ProductChangeBaseData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public int $category_id,
        public Price $price,
        public ProductStatus $status,
    ) {
    }
}