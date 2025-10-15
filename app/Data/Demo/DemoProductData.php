<?php

namespace App\Data\Demo;

use Spatie\LaravelData\Data;

class DemoProductData extends Data
{
    public function __construct(
        public string $name,
        public string $imagePath,
        public string $categoryFullPath,
    ) {}
}
