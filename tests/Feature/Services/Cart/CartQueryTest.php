<?php

namespace Tests\Feature\Services\Cart;

use App\Contracts\Cart;
use App\DTO\Cart\CartDto;
use App\DTO\Cart\CartItemDto;
use App\Models\Product;
use App\Services\Cart\CartQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CartQueryTest extends TestCase
{
    use RefreshDatabase;

    private CartQuery $cartQuery;
    private $cartMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartMock = $this->createMock(Cart::class);
        $this->cartQuery = new CartQuery($this->cartMock);
    }

    #[Test]
    public function hasReturnsTrueWhenProductExistsInCart(): void
    {
        $product = Product::factory()->create();

        $this->cartMock
            ->expects($this->once())
            ->method('has')
            ->with($product->id)
            ->willReturn(true);

        $result = $this->cartQuery->has($product);

        $this->assertTrue($result);
    }

    #[Test]
    public function hasReturnsFalseWhenProductDoesNotExistInCart(): void
    {
        $product = Product::factory()->create();

        $this->cartMock
            ->expects($this->once())
            ->method('has')
            ->with($product->id)
            ->willReturn(false);

        $result = $this->cartQuery->has($product);

        $this->assertFalse($result);
    }

    #[Test]
    public function isEmptyReturnsTrueWhenCartIsEmpty(): void
    {
        $this->cartMock
            ->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $result = $this->cartQuery->isEmpty();

        $this->assertTrue($result);
    }

    #[Test]
    public function isEmptyReturnsFalseWhenCartIsNotEmpty(): void
    {
        $this->cartMock
            ->expects($this->once())
            ->method('isEmpty')
            ->willReturn(false);

        $result = $this->cartQuery->isEmpty();

        $this->assertFalse($result);
    }

    #[Test]
    public function getQuantityForReturnsCorrectQuantity(): void
    {
        $product = Product::factory()->create();
        $expectedQuantity = 5;

        $this->cartMock
            ->expects($this->once())
            ->method('getQuantityFor')
            ->with($product->id)
            ->willReturn($expectedQuantity);

        $result = $this->cartQuery->getQuantityFor($product);

        $this->assertEquals($expectedQuantity, $result);
    }

    #[Test]
    public function getQuantityForReturnsZeroWhenProductNotInCart(): void
    {
        $product = Product::factory()->create();

        $this->cartMock
            ->expects($this->once())
            ->method('getQuantityFor')
            ->with($product->id)
            ->willReturn(0);

        $result = $this->cartQuery->getQuantityFor($product);

        $this->assertEquals(0, $result);
    }

    #[Test]
    public function allReturnsEmptyCartDtoWhenCartIsEmpty(): void
    {
        $this->cartMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $result = $this->cartQuery->all();

        $this->assertInstanceOf(CartDto::class, $result);
        $this->assertCount(0, $result->items);
    }

    #[Test]
    public function allReturnsCartDtoWithCorrectItems(): void
    {
        $cartItems = [
            1 => 2,
            2 => 3,
            3 => 1,
        ];

        $this->cartMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($cartItems);

        $result = $this->cartQuery->all();

        $this->assertInstanceOf(CartDto::class, $result);
        $this->assertCount(3, $result->items);

        foreach ($result->items as $index => $item) {
            $this->assertInstanceOf(CartItemDto::class, $item);
            $expectedId = array_keys($cartItems)[$index];
            $expectedQuantity = $cartItems[$expectedId];
            $this->assertEquals($expectedId, $item->id);
            $this->assertEquals($expectedQuantity, $item->quantity);
        }
    }

    #[Test]
    public function allReturnsCartDtoWithSingleItem(): void
    {
        $cartItems = [
            1 => 5,
        ];

        $this->cartMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($cartItems);

        $result = $this->cartQuery->all();

        $this->assertInstanceOf(CartDto::class, $result);
        $this->assertCount(1, $result->items);

        $item = $result->items->first();
        $this->assertInstanceOf(CartItemDto::class, $item);
        $this->assertEquals(1, $item->id);
        $this->assertEquals(5, $item->quantity);
    }
}