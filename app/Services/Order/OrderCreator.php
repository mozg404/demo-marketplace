<?php

namespace App\Services\Order;

use App\Collections\CreatableOrderItemCollection;
use App\Data\Orders\CreatableOrderItemData;
use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\Product\ProductSaleManager;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class OrderCreator
{
    public function __construct(
        private ProductSaleManager $saleManager,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function create(User $user, CreatableOrderItemCollection $items): Order
    {
        // Проверяем, что у товаров из списка есть нужное количество позиций на складе
        $items->each(fn(CreatableOrderItemData $item) => $this->saleManager->ensureCanByPurchased($item->product, $item->quantity));

        // Создаем новый заказ
        return DB::transaction(function () use ($user, $items) {
            $order = Order::create([
                'user_id' => $user->id,
                'amount' => $items->getTotalPrice()->getCurrentPrice(),
                'status' => OrderStatus::PENDING,
            ]);

            $items->each(function (CreatableOrderItemData $creatableItem) use ($order) {
                $reserved = $this->saleManager->reserveForOrder($creatableItem->product->id, $creatableItem->quantity);

                foreach ($reserved as $reservedItemId) {
                    $order->items()->create([
                        'product_id' => $creatableItem->product->id,
                        'stock_item_id' => $reservedItemId,
                        'current_price' => $creatableItem->product->price->getCurrentPrice(),
                        'base_price' => $creatableItem->product->price->getBasePrice(),
                    ]);
                }
            });

            return $order->fresh();
        });
    }

}