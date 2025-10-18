<?php

namespace Tests\Feature\Services\Product\Stock;

use App\Models\Product;
use App\Models\StockItem;
use App\Services\Stock\StockRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockQueryTest extends TestCase
{
    use RefreshDatabase;

    private StockRepository $stockQuery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockQuery = app(StockRepository::class);
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
