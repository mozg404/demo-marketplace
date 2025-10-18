<?php

namespace Tests\Feature\Services\Product;

use App\Models\Product;
use App\Services\Product\ProductManager;
use App\Services\Product\ProductUpdater;
use App\ValueObjects\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductUpdaterTest extends TestCase
{
    use RefreshDatabase;

    private ProductUpdater $productUpdater;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productUpdater = $this->app->make(ProductUpdater::class);
    }

    public function testCorrectUpdatePrice(): void
    {
        $product = Product::factory()->withPrice(new Price(1000))->create();
        $newPrice = new Price(931, 450);

        $this->productUpdater->updatePrice($product, $newPrice);

        $this->assertNotNull($product->price);
        $this->assertInstanceOf(Price::class, $product->price);
        $this->assertSame($newPrice->getBasePrice(), $product->price->getBasePrice());
        $this->assertSame($newPrice->getDiscountPrice(), $product->price->getDiscountPrice());
        $this->assertDatabaseHas('products', [
            'base_price' => $newPrice->getBasePrice(),
            'current_price' => $newPrice->getCurrentPrice(),
        ]);
    }
}
