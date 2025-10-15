<?php

namespace Tests\Feature\Services\Product\Stock;

use App\Exceptions\Product\NotEnoughStockException;
use App\Models\Product;
use App\Models\StockItem;
use App\Services\Product\Stock\StockChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockCheckerTest extends TestCase
{
    use RefreshDatabase;

    private StockChecker $stockChecker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockChecker = app(StockChecker::class);
    }

    public function testHasEnoughStock(): void
    {
        $product = Product::factory()->create();
        StockItem::factory(2)->for($product)->available()->create();
        StockItem::factory()->for($product)->reserved()->create();

        $this->assertTrue($this->stockChecker->hasEnoughStock($product, 2), 'Enough stock should be available');
        $this->assertFalse($this->stockChecker->hasEnoughStock($product, 3), 'Not enough stock');
    }

    public function testEnsureEnoughStock(): void
    {
        $product = Product::factory()->create();
        StockItem::factory(2)->for($product)->available()->create();
        StockItem::factory()->for($product)->reserved()->create();

        $this->expectException(NotEnoughStockException::class);

        $this->stockChecker->ensureStockAvailable($product, 3);
    }
}
