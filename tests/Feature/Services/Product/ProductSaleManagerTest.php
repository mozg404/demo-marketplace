<?php

namespace Services\Product;

use App\Enum\ProductStatus;
use App\Enum\StockItemStatus;
use App\Exceptions\Product\ProductUnavailableException;
use App\Models\Product;
use App\Models\StockItem;
use App\Services\Product\DTO\PurchasableItem;
use App\Services\Product\ProductCreator;
use App\Services\Product\ProductSaleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductSaleManagerTest extends TestCase
{
    use RefreshDatabase;

    private ProductSaleManager $saleManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saleManager = $this->app->make(ProductSaleManager::class);
    }

    #[Test]
    public function failValidationPurchasableItems(): void
    {
        $product1 = Product::factory()->create(['status' => ProductStatus::ACTIVE]);
        $product2 = Product::factory()->create(['status' => ProductStatus::ACTIVE]);
        StockItem::factory()->for($product1)->create();
        StockItem::factory()->for($product1)->create();
        StockItem::factory()->for($product2)->create();
        $items = [
            new PurchasableItem($product1->id, 1),
            new PurchasableItem($product2->id, 2),
        ];

        $this->expectException(ProductUnavailableException::class);
        $this->saleManager->validatePurchasableItems($items);
    }

    #[Test]
    public function reservePurchasableItems(): void
    {
        $product1 = Product::factory()->create(['status' => ProductStatus::ACTIVE]);
        $product2 = Product::factory()->create(['status' => ProductStatus::ACTIVE]);
        StockItem::factory()->for($product1)->create();
        StockItem::factory()->for($product1)->create();
        StockItem::factory()->for($product2)->create();
        $items = [
            new PurchasableItem($product1->id, 2),
        ];

        $reserved = $this->saleManager->reservePurchasableItems($items);

        foreach ($reserved as $product) {
            foreach ($product->reserved_items_ids as $id) {
                $this->assertDatabaseHas('stock_items', [
                    'id' => $id,
                    'status' => StockItemStatus::RESERVED->value,
                ]);
            }
        }

        dd($reserved->toArray());
    }
}
