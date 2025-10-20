<?php

namespace App\DTO\Product;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

class ProductUpdateInstructionDto extends Data
{
    public function __construct(
        #[Sometimes, Min(3)]
        public string $instruction = '',
    ) {
    }
}