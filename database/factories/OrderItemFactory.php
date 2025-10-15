<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockItem;
use App\ValueObjects\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * (!!!) НАРУШЕНИЕ КОНСИСТЕНТНОСТИ ИЗ-ЗА ДЕНОРМАЛИЗАЦИИ
 * Использовать с методами forProduct или forStockItem, иначе создаются лишние записи
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $price = Price::random();

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'stock_item_id' => StockItem::factory(),
            'current_price' => $price->getCurrentPrice(),
            'base_price' => $price->getBasePrice(),
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (OrderItem $orderItem) {
            if ($orderItem->stockItem->product_id !== $orderItem->product_id) {
                $orderItem->update([
                    'product_id' => $orderItem->stockItem->product_id
                ]);
            }
        });
    }

    public function forStockItem(StockItem $stockItem): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $stockItem->product_id,
            'stock_item_id' => $stockItem->id,
        ]);
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
            'stock_item_id' => StockItem::factory()->for($product),
        ]);
    }
}
