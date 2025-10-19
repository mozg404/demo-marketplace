<?php

namespace Tests\Feature\Services\Product;

use App\Enum\ProductStatus;
use App\Enum\StockItemStatus;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use App\Services\Product\DTO\PurchasableItem;
use App\Services\Product\ProductSaleManager;
use App\ValueObjects\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductSaleManagerTest extends TestCase
{
    use RefreshDatabase;

    private ProductSaleManager $productSaleManager;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productSaleManager = app(ProductSaleManager::class);
        $this->user = User::factory()->create();
    }

    #[Test]
    public function canValidatePurchasableItemsWithValidProducts(): void
    {
        $product1 = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
        ]);
        $product2 = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
        ]);

        StockItem::factory()->count(5)->create(['product_id' => $product1->id]);
        StockItem::factory()->count(3)->create(['product_id' => $product2->id]);

        $items = [
            new PurchasableItem($product1->id, 2),
            new PurchasableItem($product2->id, 1),
        ];

        $this->expectNotToPerformAssertions();
        $this->productSaleManager->validatePurchasableItems($items);
    }

    #[Test]
    public function validatePurchasableItemsThrowsExceptionForInactiveProduct(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::DRAFT,
        ]);

        StockItem::factory()->count(3)->create(['product_id' => $product->id]);

        $items = [new PurchasableItem($product->id, 1)];

        $this->expectException(ProductUnavailableException::class);
        $this->productSaleManager->validatePurchasableItems($items);
    }

    #[Test]
    public function validatePurchasableItemsThrowsExceptionForInsufficientQuantity(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
        ]);

        StockItem::factory()->count(2)->create(['product_id' => $product->id]);

        $items = [new PurchasableItem($product->id, 5)]; // Запрашиваем больше чем есть

        $this->expectException(ProductUnavailableException::class);
        $this->productSaleManager->validatePurchasableItems($items);
    }

    #[Test]
    public function validatePurchasableItemsThrowsExceptionForNonExistentProduct(): void
    {
        $items = [new PurchasableItem(999, 1)]; // Несуществующий ID

        $this->expectException(ProductUnavailableException::class);
        $this->productSaleManager->validatePurchasableItems($items);
    }

    #[Test]
    public function canValidatePurchasableItemsWithCollection(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
        ]);

        StockItem::factory()->count(3)->create(['product_id' => $product->id]);

        $items = collect([new PurchasableItem($product->id, 2)]);

        $this->expectNotToPerformAssertions();
        $this->productSaleManager->validatePurchasableItems($items);
    }

    #[Test]
    public function canReservePurchasableItems(): void
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

        $stockItems1 = StockItem::factory()->count(3)->create([
            'product_id' => $product1->id,
            'status' => StockItemStatus::AVAILABLE,
        ]);
        $stockItems2 = StockItem::factory()->count(2)->create([
            'product_id' => $product2->id,
            'status' => StockItemStatus::AVAILABLE,
        ]);

        $items = [
            new PurchasableItem($product1->id, 2),
            new PurchasableItem($product2->id, 1),
        ];

        $reservedProducts = $this->productSaleManager->reservePurchasableItems($items);

        $this->assertInstanceOf(Collection::class, $reservedProducts);
        $this->assertCount(2, $reservedProducts);

        // Проверяем первый продукт
        $reservedProduct1 = $reservedProducts->first(fn($item) => $item->productId === $product1->id);
        $this->assertEquals(2, $reservedProduct1->quantity);
        $this->assertEquals(1000, $reservedProduct1->price->getCurrentPrice());
        $this->assertCount(2, $reservedProduct1->stockIds);

        // Проверяем второй продукт
        $reservedProduct2 = $reservedProducts->first(fn($item) => $item->productId === $product2->id);
        $this->assertEquals(1, $reservedProduct2->quantity);
        $this->assertEquals(1200, $reservedProduct2->price->getCurrentPrice());
        $this->assertCount(1, $reservedProduct2->stockIds);

        // Проверяем, что stock items помечены как зарезервированные
        $this->assertEquals(StockItemStatus::RESERVED, $stockItems1[0]->fresh()->status);
        $this->assertEquals(StockItemStatus::RESERVED, $stockItems1[1]->fresh()->status);
        $this->assertEquals(StockItemStatus::AVAILABLE, $stockItems1[2]->fresh()->status); // Один остался доступным
        $this->assertEquals(StockItemStatus::RESERVED, $stockItems2[0]->fresh()->status);
    }

    #[Test]
    public function canReservePurchasableItemsWithCollection(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => ProductStatus::ACTIVE,
            'price' => new Price(1000),
        ]);

        StockItem::factory()->count(2)->create([
            'product_id' => $product->id,
            'status' => StockItemStatus::AVAILABLE,
        ]);

        $items = collect([new PurchasableItem($product->id, 1)]);

        $reservedProducts = $this->productSaleManager->reservePurchasableItems($items);

        $this->assertCount(1, $reservedProducts);
        $this->assertEquals($product->id, $reservedProducts->first()->productId);
    }

    #[Test]
    public function canCancelReservation(): void
    {
        $stockItem = StockItem::factory()->create([
            'status' => StockItemStatus::RESERVED,
        ]);

        $this->productSaleManager->cancelReservation($stockItem->id);

        $this->assertEquals(StockItemStatus::AVAILABLE, $stockItem->fresh()->status);
    }

    #[Test]
    public function cancelReservationWorksWithAvailableItem(): void
    {
        $stockItem = StockItem::factory()->create([
            'status' => StockItemStatus::AVAILABLE,
        ]);

        $this->productSaleManager->cancelReservation($stockItem->id);

        $this->assertEquals(StockItemStatus::AVAILABLE, $stockItem->fresh()->status);
    }
}