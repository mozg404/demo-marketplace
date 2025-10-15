<?php

namespace App\Services\Product\Stock;

use App\Models\StockItem;
use Webmozart\Assert\Assert;

class StockUpdater
{
    public function update(StockItem $stockItem, string $content): void
    {
        Assert::stringNotEmpty($content);
        Assert::minLength($content, 3);
        Assert::maxLength($content, 255);

        $stockItem->content = $content;
        $stockItem->save();
    }
}