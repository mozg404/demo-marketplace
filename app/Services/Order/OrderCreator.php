<?php

namespace App\Services\Order;

use App\Collections\CreatableOrderItemCollection;
use App\Data\Orders\CreatableOrderItemData;
use App\Enum\OrderStatus;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Order;
use App\Models\StockItem;
use App\Models\User;
use App\Services\Product\ProductAvailabilityChecker;
use App\Services\Product\Stock\StockQuery;
use App\Services\Product\Stock\StockReserver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class OrderCreator
{
    public function __construct(
        private ProductAvailabilityChecker $availabilityChecker,
        private StockQuery $stockQuery,
        private StockReserver $stockReserver,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function create(User $user, CreatableOrderItemCollection $items, ?Carbon $createdAt = null): Order
    {
        // Проверяем, что у товаров из списка есть нужное количество позиций на складе
        $items->each(fn(CreatableOrderItemData $item) => $this->availabilityChecker->ensureCanByPurchased($item->product, $item->quantity));

        // Создаем новый заказ
        return DB::transaction(function () use ($user, $items, $createdAt) {
            $order = Order::create([
                'user_id' => $user->id,
                'amount' => $items->getTotalPrice()->getCurrentPrice(),
                'status' => OrderStatus::PENDING,
                'created_at' => $createdAt ?? Carbon::now(),
            ]);

            $items->each(function (CreatableOrderItemData $creatableItem) use ($order) {
                // Запрашиваем свободные позиции со склада
                $stockItems = $this->stockQuery->getAvailableItemsFor($creatableItem->product, $creatableItem->quantity);

                // Пробегаемся по позициям и резервируем их для заказа
                $stockItems->each(function (StockItem $stockItem) use ($creatableItem, $order) {
                    // Создаем позицию заказа
                    $orderItem = $order->items()->create([
                        'product_id' => $creatableItem->product->id,
                        'stock_item_id' => $stockItem->id,
                        'current_price' => $creatableItem->product->price->getCurrentPrice(),
                        'base_price' => $creatableItem->product->price->getBasePrice(),
                    ]);

                    // Резервируем товар на складе
                    $this->stockReserver->reserve($stockItem, $orderItem);
                });
            });

            return $order->fresh();
        });
    }
}