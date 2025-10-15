<?php

namespace Tests\Feature\Services\Cart;

use App\Contracts\Cart;
use App\Models\Product;
use App\Services\Cart\CartManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartManagerTest extends TestCase
{
    use RefreshDatabase;

    public function testCorrectAdd(): void
    {
        $cartMock = $this->createMock(Cart::class);
        $product = Product::factory()->make(['id' => 123]);

        $cartMock->expects($this->once())
            ->method('add')
            ->with($product->id, 1);
        $cartService = new CartManager($cartMock);

        $cartService->add($product);
    }

    public function testAddWithCustomQuantity(): void
    {
        $cartMock = $this->createMock(Cart::class);
        $product = Product::factory()->make(['id' => 123]);
        $quantity = 5;

        $cartMock->expects($this->once())
            ->method('add')
            ->with($product->id, $quantity);
        $cartService = new CartManager($cartMock);

        $cartService->add($product, $quantity);
    }

    public function testCorrectRemove(): void
    {
        $cartMock = $this->createMock(Cart::class);
        $product = Product::factory()->make(['id' => 123]);

        $cartMock->expects($this->once())
            ->method('remove')
            ->with($product->id, 1);
        $cartService = new CartManager($cartMock);

        $cartService->remove($product);
    }

    public function testRemoveWithCustomQuantity(): void
    {
        $cartMock = $this->createMock(Cart::class);
        $product = Product::factory()->make(['id' => 123]);
        $quantity = 5;

        $cartMock->expects($this->once())
            ->method('remove')
            ->with($product->id, $quantity);
        $cartService = new CartManager($cartMock);

        $cartService->remove($product, $quantity);
    }

    public function testCorrectRemoveItem(): void
    {
        $cartMock = $this->createMock(Cart::class);
        $product = Product::factory()->make(['id' => 123]);

        $cartMock->expects($this->once())
            ->method('removeItem')
            ->with($product->id);
        $cartService = new CartManager($cartMock);

        $cartService->removeItem($product);
    }

    public function testClear(): void
    {
        $cartMock = $this->createMock(Cart::class);

        $cartMock->expects($this->once())->method('clear');
        $cartService = new CartManager($cartMock);

        $cartService->clear();
    }
}
