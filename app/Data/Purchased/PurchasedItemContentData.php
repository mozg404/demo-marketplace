<?php

namespace App\Data\Purchased;

use App\Models\OrderItem;
use Spatie\LaravelData\Data;

class PurchasedItemContentData extends Data
{
    public function __construct(
        public int $id,
        public int $product_id,
        public int $stock_item_id,
        public string $name,
        public string $content,
        public ?string $instruction,
    ) {
    }

    public static function fromModel(OrderItem $orderItem): self
    {
        return new self(
            id: $orderItem->id,
            product_id: $orderItem->product_id,
            stock_item_id: $orderItem->stock_item_id,
            name: $orderItem->product->name,
            content: $orderItem->stockItem->content,
            instruction: $orderItem->product->instruction,
        );
    }
}
