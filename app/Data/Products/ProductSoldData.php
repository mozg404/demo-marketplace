<?php

namespace App\Data\Products;

use App\Data\User\UserData;
use App\Enum\ProductStatus;
use App\Models\OrderItem;
use App\ValueObjects\Price;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ProductSoldData extends Data
{
    public function __construct(
        public int $id,
        public int $order_id,
        public int $stock_item_id,
        public string $name,
        public Price $price,
        public ?ProductPreviewData $preview,
        public ProductStatus $status,
        public ?UserData $buyer = null,
        public Carbon $sold_at,
    ) {}

    public static function fromModel(OrderItem $orderItem): self
    {
        return new self(
            id: $orderItem->product_id,
            order_id: $orderItem->order_id,
            stock_item_id: $orderItem->stock_item_id,
            name: $orderItem->product->name,
            price: $orderItem->price,
            preview: ProductPreviewData::from($orderItem->product->preview),
            status: $orderItem->product->status,
            buyer: UserData::from($orderItem->order->user),
            sold_at: $orderItem->order->paid_at
        );
    }
}
