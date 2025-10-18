<?php

namespace Services\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\Product\ProductCreator;
use App\Services\Product\ProductManager;
use App\Services\Product\ProductSaleManager;
use App\ValueObjects\Price;
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
    public function reservationForOrder(): void
    {

    }
}
