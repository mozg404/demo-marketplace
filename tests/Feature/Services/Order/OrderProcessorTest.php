<?php

namespace Tests\Feature\Services\Order;

use App\Enum\OrderStatus;
use App\Exceptions\Balance\InsufficientFundsException;
use App\Exceptions\Order\OrderAlreadyProcessedException;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use App\Services\Order\OrderProcessor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderProcessorTest extends TestCase
{
    use RefreshDatabase;

    private OrderProcessor $orderProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderProcessor = $this->app->make(OrderProcessor::class);
    }

    public function testInsufficientFunds(): void
    {
        $stock = StockItem::factory(3)->create();
        $order = Order::factory()->withStockItems($stock)->pending()->create();
        $this->expectException(InsufficientFundsException::class);
        $this->orderProcessor->process($order);
    }

    public function testOrderHasCancelledStatus(): void
    {
        $order = Order::factory()->make([
            'status' => OrderStatus::CANCELLED,
        ]);
        $this->expectException(OrderAlreadyProcessedException::class);
        $this->orderProcessor->process($order);
    }

    public function testOrderHasCompletedStatus(): void
    {
        $order = Order::factory()->make([
            'status' => OrderStatus::COMPLETED,
        ]);
        $this->expectException(OrderAlreadyProcessedException::class);
        $this->orderProcessor->process($order);
    }

    public function testCompleteProcess(): void
    {
        $seller1 = User::factory()->create();
        $seller2 = User::factory()->create();

        $product1 = Product::factory()->for($seller1)->create();
        $product2 = Product::factory()->for($seller2)->create();

        $stock = new Collection([
            StockItem::factory()->for($product1)->available()->create(),
            StockItem::factory()->for($product1)->available()->create(),
            StockItem::factory()->for($product2)->available()->create(),
        ]);

        $balanceRemaining = 100;
        $buyer = User::factory()->create([
            'balance' => $product1->price->getCurrentPrice() * 2 + $product2->price->getCurrentPrice() + $balanceRemaining
        ]);

        $order = Order::factory()->for($buyer)->pending()->withStockItems($stock)->create();

        $this->orderProcessor->process($order);

        // Статус завершенного заказа
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $buyer->id,
            'amount' => $order->amount,
            'status' => OrderStatus::COMPLETED,
        ]);

        // У покупателя на балансе правильный остаток
        $this->assertDatabaseHas('users', [
            'id' => $buyer->id,
            'balance' => $balanceRemaining,
        ]);

        // У продавцов пополнение на сумму заказа
        $this->assertDatabaseHas('users', [
            'id' => $seller1->id,
            'balance' => $product1->price->getCurrentPrice() * 2,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $seller2->id,
            'balance' => $product2->price->getCurrentPrice(),
        ]);
    }
}
