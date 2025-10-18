<?php

namespace App\Services\Stock;

use App\DTO\Product\StockCreateDto;
use App\DTO\Product\StockUpdateDto;
use App\Enum\StockItemStatus;
use App\Exceptions\Product\NotEnoughStockException;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockItem;

readonly class StockService
{
    public function __construct(
        private StockRepository $repository,
    ) {
    }

    public function createStockItem(Product $product, StockCreateDto $dto): void
    {
        $this->repository->create($product, $dto->content);
    }

    public function updateStockItem(StockItem $stockItem, StockUpdateDto $dto): void
    {
        $this->repository->update($stockItem, $dto->content);
    }

    public function reserve(StockItem $stockItem, OrderItem $orderItem): void
    {
        $this->repository->changeStatus($stockItem, StockItemStatus::RESERVED, $orderItem->id);
    }

    public function unreserve(StockItem $stockItem): void
    {
        $this->repository->changeStatus($stockItem, StockItemStatus::AVAILABLE);
    }

    public function hasEnoughStock(Product $product, int $quantity = 1): bool
    {
        return $this->repository->getAvailableCount($product) >= $quantity;
    }

    public function ensureStockAvailable(Product $product, int $quantity = 1): void
    {
        if (!$this->hasEnoughStock($product, $quantity)) {
            throw new NotEnoughStockException();
        }
    }
}