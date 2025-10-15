<?php

namespace App\Services\Product;

use App\Builders\ProductQueryBuilder;
use App\Models\Product;

class ProductQuery
{
    public function query(): ProductQueryBuilder
    {
        return Product::query();
    }
}