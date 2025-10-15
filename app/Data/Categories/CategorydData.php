<?php

namespace App\Data\Categories;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class CategorydData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}
}
