<?php

namespace Tests\Feature\Services\Order;

use App\Enum\OrderStatus;
use App\Exceptions\Product\NotEnoughStockException;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use App\Services\Order\ExpressOrderCreator;
use App\ValueObjects\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpressOrderCreatorTest extends TestCase
{
    use RefreshDatabase;

    private ExpressOrderCreator $orderCreator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderCreator = $this->app->make(ExpressOrderCreator::class);
    }

    // Недостаточно позиций на складе
    public function testNotEnoughStock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->isActive()->create();

        $this->expectException(NotEnoughStockException::class);
        $this->orderCreator->create($user, $product);
    }

    // Товар со статусом черновика
    public function testProductIsDraft(): void
    {
        $user = User::factory()->make();
        $product = Product::factory()->isDraft()->create();
        StockItem::factory()->for($product)->available()->create();

        $this->expectException(ProductUnavailableException::class);
        $this->orderCreator->create($user, $product);
    }

    // Товар на паузе
    public function testProductIsPaused(): void
    {
        $user = User::factory()->make();
        $product = Product::factory()->isPaused()->create();
        StockItem::factory()->for($product)->available()->create();

        $this->expectException(ProductUnavailableException::class);
        $this->orderCreator->create($user, $product);
    }

    // Успешное создание заказа
    public function testSuccessCreating(): void
    {
        $price = new Price(1000);
        $user = User::factory()->create();
        $product = Product::factory()->for($user)->isActive()->withPrice($price)->create();
        StockItem::factory(3)
            ->for($product)
            ->available()
            ->create();

        $order = $this->orderCreator->create($user, $product);

        $this->assertEquals($order->user->id, $user->id);
        $this->assertEquals($price->getCurrentPrice(), $order->amount);
        $this->assertEquals(OrderStatus::PENDING, $order->status);

        // Проверяем наличие в БД
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'amount' => $price->getCurrentPrice(),
        ]);

        // Проверка на резервацию
        $this->assertEquals(2, StockItem::query()->whereProduct($product)->isAvailable()->count());
        $this->assertEquals(1, StockItem::query()->whereProduct($product)->isReserved()->count());
    }
}
