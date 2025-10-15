<?php

namespace App\Services\Product\Stock;

use App\Enum\StockItemStatus;
use App\Models\OrderItem;
use App\Models\StockItem;

class StockReserver
{
    public function reserve(StockItem $stockItem, OrderItem $orderItem): void
    {
        $stockItem->status = StockItemStatus::RESERVED;
        $stockItem->order_item_id = $orderItem->id;
        $stockItem->save();
    }

    public function unreserve(StockItem $stockItem): void
    {
        $stockItem->status = StockItemStatus::AVAILABLE;
        $stockItem->order_item_id = null;
        $stockItem->save();
    }
}