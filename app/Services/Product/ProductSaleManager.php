<?php

namespace App\Services\Product;

use App\Collections\ProductCollection;
use App\DTO\Product\PurchasableItemDto;
use App\DTO\Product\ReservedProductDto;
use App\Enum\StockItemStatus;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Product;
use App\Models\StockItem;
use Illuminate\Support\Collection;

class ProductSaleManager
{
    /** @param array<PurchasableItemDto>|Collection<PurchasableItemDto> $items */
    public function validatePurchasableItems(array|Collection $items): void
    {
        $ids = collect($items)->pluck('productId')->toArray();

        /** @var ProductCollection $products */
        $products = Product::query()
            ->select(['id', 'status'])
            ->withAvailableCount()
            ->whereIds($ids)
            ->get();

        foreach ($items as $item) {
            $product = $products->where('id', $item->productId)->first();

            if (!$product || !$product->isActive() || $product->available_count < $item->quantity) {
                throw new ProductUnavailableException();
            }
        }
    }

    /**
     * @param array<PurchasableItemDto>|Collection<PurchasableItemDto> $items
     * @return Collection<ReservedProductDto>
     */
    public function reservePurchasableItems(array|Collection $items): Collection
    {
        if (is_array($items)) {
            $items = collect($items);
        }

        return Product::query()
            ->select(['id', 'current_price', 'base_price'])
            ->whereIds($items->pluck('productId')->toArray())
            ->get()
            ->map(function (Product $product) use ($items) {
                /** @param PurchasableItemDto $purchasable */
                $purchasable = $items->where('productId', $product->id)->first();

                return new ReservedProductDto(
                    $product->id,
                    $purchasable->quantity,
                    $product->price,
                    StockItem::query()
                        ->select(['id', 'product_id', 'status'])
                        ->whereProduct($product)
                        ->isAvailable()
                        ->take($purchasable->quantity)
                        ->get()
                        ->each(static function (StockItem $stockItem) {
                            $stockItem->markAsReserved();
                        })
                        ->pluck('id')
                        ->toArray(),
                );
            });
    }

    public function cancelReservation(int $stockItemId): void
    {
        StockItem::query()->whereId($stockItemId)->update(['status' => StockItemStatus::AVAILABLE]);
    }
}