<?php

namespace Tests\Feature\Services\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\Product\ProductCreator;
use App\Services\Product\ProductService;
use App\ValueObjects\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCreatorTest extends TestCase
{
    use RefreshDatabase;

    private ProductCreator $productCreator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCreator = $this->app->make(ProductCreator::class);
    }

    public function testCorrectCreateBaseProduct(): void
    {
        $user = User::factory()->create();
        $name = 'Тестовый товар';
        $price = new Price(100, 50);
        $category = Category::factory()->create();

        $product = $this->productCreator->createBaseProduct($user, $category, $name, $price);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame($product->user_id, $user->id);
        $this->assertSame($name, $product->name);
        $this->assertSame(50, $product->price->getCurrentPrice());
        $this->assertSame(100, $product->price->getBasePrice());
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'user_id' => $user->id,
            'base_price' => $price->getBasePrice(),
            'current_price' => $price->getCurrentPrice(),
        ]);
    }
}
