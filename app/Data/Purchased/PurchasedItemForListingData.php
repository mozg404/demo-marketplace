<?php

namespace App\Data\Purchased;

use App\Data\Categories\CategorydData;
use App\Data\Products\ProductPreviewData;
use App\Data\User\UserData;
use App\Enum\ProductStatus;
use App\Models\Feedback;
use App\Models\OrderItem;
use App\ValueObjects\Price;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class PurchasedItemForListingData extends Data
{
    public function __construct(
        public int $id,
        public int $order_id,
        public int $order_item_id,
        public int $stock_item_id,
        public int $product_id,
        public string $name,
        public Price $price,
        public ?ProductPreviewData $preview,
        public ?CategorydData $category,
        public ProductStatus $status,
        public ?UserData $seller = null,
        public ?Feedback $feedback = null,
        public Carbon $purchased_at,
    ) {
    }

    public static function fromModel(OrderItem $orderItem): self
    {
        return new self(
            id: $orderItem->id,
            order_id: $orderItem->order_id,
            order_item_id: $orderItem->id,
            stock_item_id: $orderItem->stock_item_id,
            product_id: $orderItem->product_id,
            name: $orderItem->product->name,
            price: $orderItem->price,
            preview: ProductPreviewData::from($orderItem->product?->preview),
            category: CategorydData::from($orderItem->product->category),
            status: $orderItem->product->status,
            seller: UserData::from($orderItem->seller),
            feedback: $orderItem->feedback,
            purchased_at: $orderItem->order->paid_at
        );
    }
}
