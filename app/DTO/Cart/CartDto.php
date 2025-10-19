<?php

namespace App\DTO\Cart;

use App\Services\Product\DTO\PurchasableItem;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CartDto extends Data
{
    public function __construct(
        #[DataCollectionOf(CartItemDto::class)]
        public readonly DataCollection $items,
    ) {
    }

    public function toPurchasableItems(): Collection
    {
        return $this->items->toCollection()->map(fn(CartItemDto $dto) => new PurchasableItem($dto->id, $dto->quantity));
    }
}