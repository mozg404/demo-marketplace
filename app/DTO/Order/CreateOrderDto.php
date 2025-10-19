<?php

namespace App\DTO\Order;

use App\Services\Product\DTO\PurchasableItem;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CreateOrderDto extends Data
{
    public function __construct(
        #[DataCollectionOf(PurchasableItem::class)]
        public readonly DataCollection $items,
    ) {
    }
}