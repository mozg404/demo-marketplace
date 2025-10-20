<?php

namespace App\DTO\Product;

use App\Enum\ProductStatus;
use App\ValueObjects\Price;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class ProductUpdateBaseDto extends Data
{
    public function __construct(
        #[Min(3), Max(255)]
        public string $name,
        public Price $price,
        #[Exists('categories', 'id')]
        public int $category_id,
        #[Enum(ProductStatus::class)]
        public ProductStatus $status,
    ) {
    }
}