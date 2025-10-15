<?php

namespace Tests\Feature\Services\Order;

use App\Enum\OrderStatus;
use App\Enum\StockItemStatus;
use App\Exceptions\Order\CompletedOrderCannotBeCanceledException;
use App\Exceptions\Order\OrderAlreadyCanceledException;
use App\Models\Order;
use App\Models\StockItem;
use App\Services\Order\OrderCancelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancelServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderCancelService $orderCancelService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderCancelService = $this->app->make(OrderCancelService::class);
    }

    public function testCorrectCancelOrder(): void
    {
        $stockItems = StockItem::factory(3)->available()->create();
        $order = Order::factory()->withStockItems($stockItems)->pending()->create();

        $this->orderCancelService->cancelOrder($order);

        // Статус у модели
        $this->assertEquals(OrderStatus::CANCELLED, $order->status);

        // Значение в базе
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CANCELLED->value,
        ]);

        // Отсутствие статуса резервации у товаров на складе
        foreach ($stockItems as $stockItem) {
            $this->assertDatabaseHas('stock_items', [
                'id' => $stockItem->id,
                'status' => StockItemStatus::AVAILABLE->value,
            ]);
        }
    }

    public function testOrderAlreadyCompleted(): void
    {
        $order = Order::factory()->completed()->make();

        $this->expectException(CompletedOrderCannotBeCanceledException::class);

        $this->orderCancelService->cancelOrder($order);
    }

    public function testOrderAlreadyCancelled(): void
    {
        $order = Order::factory()->cancelled()->make();

        $this->expectException(OrderAlreadyCanceledException::class);

        $this->orderCancelService->cancelOrder($order);
    }
}
