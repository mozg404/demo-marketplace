<?php

namespace App\Services\Order;

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Services\Price\PriceService;
use App\Services\Product\DTO\PurchasableItem;
use App\Services\Product\ProductSaleManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class OrderCreator
{
    public function __construct(
        private ProductSaleManager $saleManager,
        private PriceService $priceService,
    ) {
    }

    /**
     * @param int $userId
     * @param array<PurchasableItem>|Collection<PurchasableItem> $items
     * @return Order
     * @throws Throwable
     */
    public function create(int $userId, Collection|array $items): Order
    {
        $this->saleManager->validatePurchasableItems($items);

        return DB::transaction(function () use ($userId, $items) {
            $reservedProducts = $this->saleManager->reservePurchasableItems($items);
            $order = Order::create([
                'user_id' => $userId,
                'amount' => $this->priceService->calculateTotalQuantityPrice($reservedProducts)->getCurrentPrice(),
                'status' => OrderStatus::PENDING,
            ]);

            foreach ($reservedProducts as $reservedProduct) {
                foreach ($reservedProduct->stockIds as $stockId) {
                    $order->items()->create([
                        'product_id' => $reservedProduct->productId,
                        'stock_item_id' => $stockId,
                        'current_price' => $reservedProduct->getPrice()->getCurrentPrice(),
                        'base_price' => $reservedProduct->getPrice()->getBasePrice(),
                    ]);
                }
            }

            return $order->fresh();
        });
    }

    public function createExpress(int $userId, PurchasableItem $item): Order
    {
        return $this->create($userId, [$item]);
    }
}