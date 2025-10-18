<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class StockCreateDto extends Data
{
    public function __construct(
        #[Min(3), Max(255)]
        public string $content,
    ) {
    }
}