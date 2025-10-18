<?php

namespace App\DTO\Product;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class ProductAttachPreviewDto extends Data
{
    public function __construct(
        #[Image, Max(5120)]
        public UploadedFile $image,
    ) {
    }
}