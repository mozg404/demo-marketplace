<?php

namespace App\Services\Product;

use App\Enum\StockItemStatus;
use App\Exceptions\Product\NotEnoughStockException;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Product;
use App\Models\StockItem;

class ProductSaleManager
{
    public function reserveForOrder(int $productId, int $quantity = 1): array
    {
        $reserved = StockItem::query()
            ->whereProduct($productId)
            ->isAvailable()
            ->take($quantity)
            ->getIds();

        if (count($reserved) !== $quantity) {
            throw new NotEnoughStockException();
        }

        foreach ($reserved as $id) {
            StockItem::query()->whereId($id)->update(['status' => StockItemStatus::RESERVED]);
        }

        return $reserved;
    }

    public function cancelReservation(int $stockItemId): void
    {
        StockItem::query()->whereId($stockItemId)->update(['status' => StockItemStatus::AVAILABLE]);
    }

    public function getAvailableCount(Product $product): int
    {
        return $product->stockItems()->isAvailable()->count();
    }

    public function hasEnoughStock(Product $product, int $quantity = 1): bool
    {
        return $this->getAvailableCount($product) >= $quantity;
    }

    public function ensureCanByPurchased(Product $product, int $quantity = 1): void
    {
        if (!$product->isActive()) {
            throw new ProductUnavailableException();
        }

        if (!$this->hasEnoughStock($product, $quantity)) {
            throw new NotEnoughStockException();
        }
    }
}