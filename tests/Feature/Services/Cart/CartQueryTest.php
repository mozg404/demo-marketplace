<?php

namespace Tests\Feature\Services\Cart;

use App\Contracts\Cart;
use App\Models\Product;
use App\Services\Cart\CartManager;
use App\Services\Cart\CartQuery;
use App\Services\Product\ProductQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartQueryTest extends TestCase
{
    use RefreshDatabase;

    private CartManager $cartManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartManager = $this->app->make(CartManager::class);
    }

    public function testHas(): void
    {
        $cartMock = $this->createMock(Cart::class);
        $productQueryMock = $this->createMock(ProductQuery::class);
        $product = Product::factory()->create(['id' => 123]);

        $cartMock->expects($this->once())
            ->method('has')
            ->with($product->id);
        $cartQuery = new CartQuery($cartMock, $productQueryMock);

        $cartQuery->has($product);
    }

    public function testIsEmpty(): void
    {
        $cartMock = $this->createMock(Cart::class);
        $productQueryMock = $this->createMock(ProductQuery::class);

        $cartMock->expects($this->once())->method('isEmpty');
        $cartQuery = new CartQuery($cartMock, $productQueryMock);

        $cartQuery->isEmpty();
    }

    public function testAll(): void
    {
        $product = Product::factory()->create(['id' => 123]);
        $quantity = 5;
        app(CartManager::class)->add($product, $quantity);
        $cartQuery = app(CartQuery::class);

        $cart = $cartQuery->all();

        $this->assertCount(1, $cart->items);
        $this->assertEquals($product->id, $cart->items[0]->product->id);
        $this->assertEquals($quantity, $cart->items[0]->quantity);
    }
}
