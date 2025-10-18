<?php

namespace App\DTO\Order;

use App\DTO\Product\PurchasableItemDto;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CreateOrderDto extends Data
{
    public function __construct(
        #[DataCollectionOf(PurchasableItemDto::class)]
        public readonly DataCollection $items,
    ) {
    }
}