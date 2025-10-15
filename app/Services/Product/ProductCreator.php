<?php

namespace App\Services\Product;

use App\Enum\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\ValueObjects\Price;
use Carbon\Carbon;

class ProductCreator
{
    public function createBaseProduct(User $user, Category $category, string $name, Price $price): Product
    {
        return $this->create($user, $category, $name, $price);
    }

    public function create(
        User $user,
        Category $category,
        string $name,
        Price $price,
        ProductStatus $status = ProductStatus::DRAFT,
        ?string $description = null,
        ?string $instruction = null,
        ?Carbon $createdAt = null,
    ): Product {
        $product = new Product();
        $product->user_id = $user->id;
        $product->category_id = $category->id;
        $product->status = ProductStatus::DRAFT;
        $product->name = $name;
        $product->price = $price;
        $product->status = $status;

        if (isset($description)) {
            $product->description = $description;
        }

        if (isset($instruction)) {
            $product->instruction = $instruction;
        }

        if (isset($createdAt)) {
            $product->created_at = $createdAt;
        }

        $product->save();

        return $product;
    }
}