<?php

namespace App\Services\Stock;

use App\Builders\StockItemQueryBuilder;
use App\Enum\StockItemStatus;
use App\Exceptions\Product\NotEnoughStockException;
use App\Models\Product;
use App\Models\StockItem;
use Illuminate\Database\Eloquent\Collection;

class StockRepository
{
    public function query(): StockItemQueryBuilder
    {
        return StockItem::query();
    }

    public function create(Product $product, string $content): StockItem
    {
        return StockItem::query()->create([
            'product_id' => $product->id,
            'content' => $content,
        ]);
    }

    public function update(StockItem $item, string $content): void
    {
        $item->content = $content;
        $item->save();
    }

    public function changeStatus(StockItem $item, StockItemStatus $status, ?int $orderItemId = null): void
    {
        $item->status = $status;
        $item->order_item_id = $orderItemId;
        $item->save();
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