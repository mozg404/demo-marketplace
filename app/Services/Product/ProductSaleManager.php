<?php

namespace App\Services\Product;

use App\Collections\ProductCollection;
use App\Enum\StockItemStatus;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Product;
use App\Models\StockItem;
use App\Services\Product\DTO\PurchasableItem;
use App\Services\Product\DTO\ReservedProduct;
use Illuminate\Support\Collection;

class ProductSaleManager
{
    /** @param array<PurchasableItem>|Collection<PurchasableItem> $items */
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
     * @param array<PurchasableItem>|Collection<PurchasableItem> $items
     * @return Collection<ReservedProduct>
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
                /** @param PurchasableItem $purchasable */
                $purchasable = $items->where('productId', $product->id)->first();

                return new ReservedProduct(
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