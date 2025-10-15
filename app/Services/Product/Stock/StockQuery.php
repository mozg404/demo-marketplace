<?php

namespace App\Services\Product\Stock;

use App\Builders\StockItemQueryBuilder;
use App\Exceptions\Product\NotEnoughStockException;
use App\Models\Product;
use App\Models\StockItem;
use Illuminate\Database\Eloquent\Collection;

class StockQuery
{
    public function query(): StockItemQueryBuilder
    {
        return StockItem::query();
    }

    public function getAvailableCount(Product $product): int
    {
        return $product->stockItems()->isAvailable()->count();
    }

    /**
     * Возвращает $quantity доступных позиций со склада
     */
    public function getAvailableItemsFor(Product $product, int $quantity = 1): Collection
    {
        $items = $product->stockItems()
            ->isAvailable()
            ->take($quantity)
            ->get();

        if ($items->count() < $quantity) {
            throw new NotEnoughStockException();
        }

        return $items;
    }
}