<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Data;

class ProductAttachFeaturesDto extends Data
{
    public function __construct(
        public array $features = [],
    ) {
    }
}