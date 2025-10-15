<?php

namespace Tests\Feature\Services\Cart;

use App\Services\Cart\SessionCart;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SessionCartTest extends TestCase
{
    private SessionCart $cart;

    protected function setUp(): void
    {
        parent::setUp();
        Session::flush();

        $this->cart = $this->app->make(SessionCart::class);
    }

    public static function invalidIdsProvider(): array
    {
        return [
            'Negative ID' => [-14, 'ID cannot be negative'],
            'Zero ID' => [0, 'ID cannot be zero'],
        ];
    }

    public static function invalidQuantitiesProvider(): array
    {
        return [
            'Negative quantity' => [-5, 'Quantity cannot be negative'],
            'Zero quantity' => [0, 'Quantity cannot be zero'],
        ];
    }

    private function assertException(
        string $method,
        string $expectedMessage,
        string $exceptionClass = \InvalidArgumentException::class,
        ...$args
    ): void {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($expectedMessage);
        $this->cart->{$method}(...$args);
    }

    // -------------------------------------------
    // Add
    // -------------------------------------------

    public function testCorrectAdd(): void
    {
        $this->cart->add(1);
        $this->cart->add(2, 2);
        $result = [
            1 => 1,
            2 => 2,
        ];

        $this->assertEquals($result, Session::get(SessionCart::SESSION_KEY));
    }

    #[DataProvider('invalidIdsProvider')]
    public function testAddInvalidId(int $invalidId, string $message): void
    {
        $this->assertException('add', $message, \InvalidArgumentException::class, $invalidId);
    }

    #[DataProvider('invalidQuantitiesProvider')]
    public function testAddInvalidQuantity(int $invalidQuantity, string $message): void
    {
        $this->assertException('add', $message, \InvalidArgumentException::class, 1, $invalidQuantity);
    }

    // -------------------------------------------
    // Has
    // -------------------------------------------

    public function testCorrectHas(): void
    {
        $this->cart->add(1);
        $this->cart->add(2, 2);

        $this->assertTrue($this->cart->has(1));
        $this->assertTrue($this->cart->has(2));
        $this->assertFalse($this->cart->has(3));
    }

    #[DataProvider('invalidIdsProvider')]
    public function testHasInvalidId(int $invalidId, string $message): void
    {
        $this->assertException('has', $message, \InvalidArgumentException::class, $invalidId);
    }

    // -------------------------------------------
    // getQuantityFor
    // -------------------------------------------

    public function testCorrectGetQuantity(): void
    {
        $this->cart->add(1);
        $this->cart->add(2, 2);

        $this->assertEquals(1, $this->cart->getQuantityFor(1));
        $this->assertEquals(2, $this->cart->getQuantityFor(2));
    }

    #[DataProvider('invalidIdsProvider')]
    public function testInvalidIdForGetQuantity(int $invalidId, string $message): void
    {
        $this->assertException('getQuantityFor', $message, \InvalidArgumentException::class, $invalidId);
    }

    // -------------------------------------------
    // remove
    // -------------------------------------------

    public function testCorrectRemove(): void
    {
        // Полностью удалено, если количество совпадает с количеством удаления
        $this->cart->add(1, 2);
        $this->cart->remove(1, 2);
        $this->assertFalse($this->cart->has(1));

        // Если имеется больше - уменьшаем
        $this->cart->add(1, 4);
        $this->cart->remove(1, 2);
        $this->assertTrue($this->cart->has(1));
        $this->assertEquals(2, $this->cart->getQuantityFor(1));

        // Если удаляется больше имеющегося
        $this->cart->add(1, 4);
        $this->cart->remove(1, 6);
        $this->assertFalse($this->cart->has(1));
    }

    #[DataProvider('invalidIdsProvider')]
    public function testInvalidIdForRemove(int $invalidId, string $message): void
    {
        $this->assertException('add', $message, \InvalidArgumentException::class, $invalidId);
    }

    #[DataProvider('invalidQuantitiesProvider')]
    public function testInvalidQuantityForRemove(int $invalidQuantity, string $message): void
    {
        $this->assertException('add', $message, \InvalidArgumentException::class, 1, $invalidQuantity);
    }

    // -------------------------------------------
    // clear
    // -------------------------------------------

    public function testCorrectClear(): void
    {
        // Полностью удалено, если количество совпадает с количеством удаления
        $this->cart->add(1);
        $this->cart->add(2,3);

        $this->cart->clear();

        $this->assertEmpty(Session::get(SessionCart::SESSION_KEY));
    }

    // -------------------------------------------
    // removeItem
    // -------------------------------------------

    public function testCorrectRemoveItem(): void
    {
        $this->cart->add(1);
        $this->cart->add(2,3);

        $this->assertTrue($this->cart->has(1));
        $this->cart->removeItem(1);
        $this->assertFalse($this->cart->has(1));

        $this->assertTrue($this->cart->has(2));
        $this->cart->removeItem(2);
        $this->assertFalse($this->cart->has(2));
    }

    #[DataProvider('invalidIdsProvider')]
    public function testInvalidIdForRemoveItem(int $invalidId, string $message): void
    {
        $this->assertException('removeItem', $message, \InvalidArgumentException::class, $invalidId);
    }

    // -------------------------------------------
    // getItems
    // -------------------------------------------

    public function testCorrectGetItems(): void
    {
        // Корзина пустая
        $this->assertEquals([], $this->cart->getItems());

        $this->cart->add(1);
        $this->cart->add(2, 2);
        $result = [
            1 => 1,
            2 => 2,
        ];

        $this->assertEquals($result, $this->cart->getItems());
        $this->assertEquals(Session::get(SessionCart::SESSION_KEY), $this->cart->getItems());
    }

    // -------------------------------------------
    // isEmpty
    // -------------------------------------------

    public function testEmptyCartState(): void
    {
        $this->assertTrue($this->cart->isEmpty());
        $this->cart->add(1);
        $this->assertFalse($this->cart->isEmpty());
    }

    // -------------------------------------------
    // getIds
    // -------------------------------------------

    public function testCorrectGetIds(): void
    {
        // Корзина пустая
        $this->assertEquals([], $this->cart->getIds());

        $this->cart->add(1);
        $this->cart->add(2, 2);
        $this->cart->add(5, 21);

        $this->assertEquals([1,2,5], $this->cart->getIds());
    }
}
