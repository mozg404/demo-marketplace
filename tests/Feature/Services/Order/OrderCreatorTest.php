<?php

namespace Tests\Feature\Services\Order;

use App\Enum\OrderStatus;
use App\Enum\ProductStatus;
use App\Enum\StockItemStatus;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use App\Services\Order\OrderCreator;
use App\Services\Product\DTO\PurchasableItem;
use App\ValueObjects\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderCreatorTest extends TestCase
{
    use RefreshDatabase;

    private OrderCreator $orderCreator;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderCreator = app(OrderCreator::class);
        $this->user = User::factory()->create();
    }

    #[Test]
    public function canCreateOrderWithMultipleProducts(): void
    {
        $product1 = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
            'price' => new Price(1000),
        ]);
        $product2 = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
            'price' => new Price(1500, 1200),
        ]);

        StockItem::factory()->count(3)->create(['product_id' => $product1->id]);
        StockItem::factory()->count(2)->create(['product_id' => $product2->id]);

        $items = [
            new PurchasableItem($product1->id, 2),
            new PurchasableItem($product2->id, 1),
        ];

        $order = $this->orderCreator->create($this->user->id, $items);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($this->user->id, $order->user_id);
        $this->assertEquals(OrderStatus::PENDING, $order->status);
        $this->assertEquals(3200, $order->amount); // (1000 * 2) + 1200 = 3200

        // Проверяем order items
        $this->assertCount(3, $order->items); // 2 items from product1 + 1 from product2

        $product1Items = $order->items->where('product_id', $product1->id);
        $product2Items = $order->items->where('product_id', $product2->id);

        $this->assertCount(2, $product1Items);
        $this->assertCount(1, $product2Items);

        // Проверяем цены в order items
        foreach ($product1Items as $item) {
            $this->assertEquals(1000, $item->current_price);
            $this->assertEquals(1000, $item->base_price);
        }

        foreach ($product2Items as $item) {
            $this->assertEquals(1200, $item->current_price);
            $this->assertEquals(1500, $item->base_price);
        }

        // Проверяем, что stock items зарезервированы
        $stockItemIds = $order->items->pluck('stock_item_id');
        $reservedStockItems = StockItem::whereIn('id', $stockItemIds)->get();
        $this->assertTrue($reservedStockItems->every(fn($item) => $item->status === StockItemStatus::RESERVED));
    }

    #[Test]
    public function canCreateOrderWithCollection(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
            'price' => new Price(500),
        ]);

        StockItem::factory()->count(2)->create(['product_id' => $product->id]);

        $items = collect([new PurchasableItem($product->id, 1)]);

        $order = $this->orderCreator->create($this->user->id, $items);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($this->user->id, $order->user_id);
        $this->assertEquals(500, $order->amount);
        $this->assertCount(1, $order->items);
    }

    #[Test]
    public function canCreateExpressOrder(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
            'price' => new Price(750),
        ]);

        StockItem::factory()->count(1)->create(['product_id' => $product->id]);

        $item = new PurchasableItem($product->id, 1);

        $order = $this->orderCreator->createExpress($this->user->id, $item);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($this->user->id, $order->user_id);
        $this->assertEquals(750, $order->amount);
        $this->assertCount(1, $order->items);
        $this->assertEquals($product->id, $order->items->first()->product_id);
    }

    #[Test]
    public function createOrderThrowsExceptionForUnavailableProduct(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::DRAFT, // Неактивный продукт
        ]);

        StockItem::factory()->count(1)->create(['product_id' => $product->id]);

        $items = [new PurchasableItem($product->id, 1)];

        $this->expectException(ProductUnavailableException::class);
        $this->orderCreator->create($this->user->id, $items);
    }

    #[Test]
    public function createOrderThrowsExceptionForInsufficientStock(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
        ]);

        StockItem::factory()->count(1)->create(['product_id' => $product->id]);

        $items = [new PurchasableItem($product->id, 2)]; // Запрашиваем больше чем есть

        $this->expectException(ProductUnavailableException::class);
        $this->orderCreator->create($this->user->id, $items);
    }

    #[Test]
    public function createOrderRollsBackOnFailure(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
        ]);

        StockItem::factory()->count(1)->create(['product_id' => $product->id]);

        // Симулируем ситуацию, где создание order items может упасть
        // Для этого создаем невалидные данные (несуществующий product_id)
        $items = [new PurchasableItem(999, 1)]; // Несуществующий продукт

        try {
            $this->orderCreator->create($this->user->id, $items);
        } catch (ProductUnavailableException $e) {
            // Ожидаемое исключение
        }

        // Проверяем, что order не был создан
        $this->assertEquals(0, Order::where('user_id', $this->user->id)->count());
        $this->assertEquals(0, \App\Models\OrderItem::count());
    }

    #[Test]
    public function canCreateOrderWithSingleProductMultipleQuantity(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
            'price' => new Price(300),
        ]);

        StockItem::factory()->count(5)->create(['product_id' => $product->id]);

        $items = [new PurchasableItem($product->id, 3)];

        $order = $this->orderCreator->create($this->user->id, $items);

        $this->assertEquals(900, $order->amount); // 300 * 3
        $this->assertCount(3, $order->items);
        $this->assertEquals(3, $order->items->where('product_id', $product->id)->count());
    }

    #[Test]
    public function canCreateOrderWithDiscountedProducts(): void
    {
        $product1 = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
            'price' => new Price(1000, 800), // 20% скидка
        ]);
        $product2 = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
            'price' => new Price(500), // Без скидки
        ]);

        StockItem::factory()->count(2)->create(['product_id' => $product1->id]);
        StockItem::factory()->count(1)->create(['product_id' => $product2->id]);

        $items = [
            new PurchasableItem($product1->id, 2), // 1600
            new PurchasableItem($product2->id, 1), // 500
        ];

        $order = $this->orderCreator->create($this->user->id, $items);

        $this->assertEquals(2100, $order->amount); // 1600 + 500
    }
}