<?php

namespace Tests\Feature\Services\Product\Stock;

use App\Models\Product;
use App\Models\StockItem;
use App\Services\Product\Stock\StockQuery;
use App\Services\Product\Stock\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockQueryTest extends TestCase
{
    use RefreshDatabase;

    private StockQuery $stockQuery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockQuery = app(StockQuery::class);
    }

    public function testGetAvailableStockCount()
    {
        $product = Product::factory()->create();
        StockItem::factory()->for($product)->available()->create();
        StockItem::factory()->for($product)->reserved()->create();
        StockItem::factory()->for($product)->available()->create();

        $this->assertEquals(2, $this->stockQuery->getAvailableCount($product));
    }
}
