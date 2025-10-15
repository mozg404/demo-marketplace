<?php

namespace App\Data\Products;

use Spatie\LaravelData\Data;

class ProductPreviewData extends Data
{
    public function __construct(
        public string $thumb,
        public string $small,
        public string $medium,
        public string $large,
        public string $original,
    ) {
    }
}
