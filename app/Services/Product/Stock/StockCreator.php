<?php

namespace App\Services\Product\Stock;

use App\Enum\StockItemStatus;
use App\Models\Product;
use App\Models\StockItem;
use Webmozart\Assert\Assert;

class StockCreator
{
    public function create(Product $product, string $content): StockItem
    {
        Assert::stringNotEmpty($content);
        Assert::minLength($content, 3);
        Assert::maxLength($content, 255);

        return $product->stockItems()->create([
            'content' => $content,
            'status' => StockItemStatus::AVAILABLE,
        ]);
    }
}