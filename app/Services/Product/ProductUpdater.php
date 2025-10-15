<?php

namespace App\Services\Product;

use App\Models\Category;
use App\Models\Product;
use App\ValueObjects\Price;

class ProductUpdater
{
    public function updateName(Product $product, string $newName): void
    {
        $product->name = $newName;
        $product->save();
    }

    public function updateCategory(Product $product, Category $category): void
    {
        $product->category_id = $category->id;
        $product->save();
    }

    public function updatePrice(Product $product, Price $price): void
    {
        $product->price = $price;
        $product->save();
    }

    public function updateDescription(Product $product, string $description): void
    {
        $product->description = $description;
        $product->save();
    }

    public function updateInstruction(Product $product, string $instruction): void
    {
        $product->instruction = $instruction;
        $product->save();
    }
}