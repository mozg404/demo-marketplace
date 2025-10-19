<?php

namespace Tests\Feature\Services\Price;

use App\Contracts\HasQuantityPrice;
use App\Services\Price\PriceService;
use App\ValueObjects\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

// Mock класс для тестирования
class TestPurchasableItem implements HasQuantityPrice
{
    public function __construct(
        private Price $price,
        private int $quantity
    ) {
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

class PriceServiceTest extends TestCase
{
    use RefreshDatabase;

    private PriceService $priceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priceService = app(PriceService::class);
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithArray(): void
    {
        $items = [
            new TestPurchasableItem(new Price(1000), 2),        // current: 2000, base: 2000
            new TestPurchasableItem(new Price(1500, 1200), 1),  // current: 1200, base: 1500
            new TestPurchasableItem(new Price(500), 3),         // current: 1500, base: 1500
        ];

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(4700, $total->getCurrentPrice());
        $this->assertEquals(5000, $total->getBasePrice()); // 2000 + 1500 + 1500 = 5000
        $this->assertTrue($total->hasDiscount());
        $this->assertEquals(300, $total->getDiscountAmount()); // 5000 - 4700 = 300
        $this->assertEquals(6, $total->getDiscountPercent());  // (300 / 5000) * 100 ≈ 6%
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithCollection(): void
    {
        $items = collect([
            new TestPurchasableItem(new Price(2000), 1), // 2000
            new TestPurchasableItem(new Price(1000, 800), 2), // 1600
        ]);

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(3600, $total->getCurrentPrice());
        $this->assertEquals(4000, $total->getBasePrice());
        $this->assertEquals(400, $total->getDiscountAmount());
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithSingleItem(): void
    {
        $items = [new TestPurchasableItem(new Price(1000), 1)];

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(1000, $total->getCurrentPrice());
        $this->assertEquals(1000, $total->getBasePrice());
        $this->assertFalse($total->hasDiscount());
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithNoDiscounts(): void
    {
        $items = [
            new TestPurchasableItem(new Price(1000), 2), // 2000
            new TestPurchasableItem(new Price(500), 2), // 1000
        ];

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(3000, $total->getCurrentPrice());
        $this->assertEquals(3000, $total->getBasePrice());
        $this->assertFalse($total->hasDiscount());
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithMixedDiscounts(): void
    {
        $items = [
            new TestPurchasableItem(new Price(1000), 2), // 2000 (без скидки)
            new TestPurchasableItem(new Price(2000, 1500), 1), // 1500 (со скидкой)
            new TestPurchasableItem(new Price(500, 400), 4), // 1600 (со скидкой)
        ];

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(5100, $total->getCurrentPrice());
        $this->assertEquals(6000, $total->getBasePrice());
        $this->assertEquals(900, $total->getDiscountAmount());
        $this->assertEquals(15, $total->getDiscountPercent());
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithEmptyArray(): void
    {
        $items = [];

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(0, $total->getCurrentPrice());
        $this->assertEquals(0, $total->getBasePrice());
        $this->assertFalse($total->hasDiscount());
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithEmptyCollection(): void
    {
        $items = collect();

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(0, $total->getCurrentPrice());
        $this->assertEquals(0, $total->getBasePrice());
        $this->assertFalse($total->hasDiscount());
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithZeroQuantity(): void
    {
        $items = [
            new TestPurchasableItem(new Price(1000), 0), // 0
            new TestPurchasableItem(new Price(500), 2), // 1000
        ];

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(1000, $total->getCurrentPrice());
        $this->assertEquals(1000, $total->getBasePrice());
    }

    #[Test]
    public function canCalculateTotalQuantityPriceWithLargeQuantities(): void
    {
        $items = [
            new TestPurchasableItem(new Price(100), 100), // 10000
            new TestPurchasableItem(new Price(50, 40), 50), // 2000
        ];

        $total = $this->priceService->calculateTotalQuantityPrice($items);

        $this->assertEquals(12000, $total->getCurrentPrice());
        $this->assertEquals(12500, $total->getBasePrice());
    }
}