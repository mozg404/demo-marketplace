<?php

namespace Tests\Feature\Services\Product;

use App\DTO\Product\ProductAttachFeaturesDto;
use App\DTO\Product\ProductBaseCreateDto;
use App\DTO\Product\ProductUpdateBaseDto;
use App\DTO\Product\ProductUpdateDescriptionDto;
use App\DTO\Product\ProductUpdateInstructionDto;
use App\DTO\Product\StockCreateDto;
use App\Enum\ProductStatus;
use App\Enum\StockItemStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use App\Services\Product\ProductManager;
use App\ValueObjects\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductManagerTest extends TestCase
{
    use RefreshDatabase;

    private ProductManager $productManager;
    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productManager = app(ProductManager::class);
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    #[Test]
    public function canCreateProductWithMinimalData(): void
    {
        $price = new Price(1000);

        $product = $this->productManager->createProduct(
            userId: $this->user->id,
            categoryId: $this->category->id,
            name: 'Test Product',
            price: $price,
        );

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($this->user->id, $product->user_id);
        $this->assertEquals($this->category->id, $product->category_id);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals($price->getCurrentPrice(), $product->price->getCurrentPrice());
        $this->assertEquals(ProductStatus::DRAFT, $product->status);
        $this->assertNull($product->description);
        $this->assertNull($product->instruction);
    }

    #[Test]
    public function canCreateProductWithAllData(): void
    {
        $price = new Price(1500, 1200);
        $createdAt = now()->subDay();

        $product = $this->productManager->createProduct(
            userId: $this->user->id,
            categoryId: $this->category->id,
            name: 'Test Product',
            price: $price,
            status: ProductStatus::ACTIVE,
            description: 'Test description',
            instruction: 'Test instruction',
            createdAt: $createdAt
        );

        $this->assertEquals(ProductStatus::ACTIVE, $product->status);
        $this->assertEquals('Test description', $product->description);
        $this->assertEquals('Test instruction', $product->instruction);
        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $product->created_at->format('Y-m-d H:i:s'));
        $this->assertTrue($product->price->hasDiscount());
        $this->assertEquals(1200, $product->price->getCurrentPrice());
        $this->assertEquals(1500, $product->price->getBasePrice());
    }

    #[Test]
    public function canCreateBaseProductUsingDto(): void
    {
        $dto = new ProductBaseCreateDto(
            category_id: $this->category->id,
            name: 'Base Product',
            price: new Price(2000)
        );

        $product = $this->productManager->createBaseProduct($this->user->id, $dto);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($this->user->id, $product->user_id);
        $this->assertEquals($dto->category_id, $product->category_id);
        $this->assertEquals($dto->name, $product->name);
        $this->assertEquals($dto->price->getCurrentPrice(), $product->price->getCurrentPrice());
        $this->assertEquals(ProductStatus::DRAFT, $product->status);
    }

    #[Test]
    public function canUpdateBaseProduct(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $newCategory = Category::factory()->create();
        $dto = new ProductUpdateBaseDto(
            name: 'Updated Product Name',
            price: new Price(2500, 2000),
            category_id: $newCategory->id,
            status: ProductStatus::ACTIVE
        );

        $this->productManager->updateBaseProduct($product, $dto);

        $product->refresh();
        $this->assertEquals($dto->category_id, $product->category_id);
        $this->assertEquals($dto->name, $product->name);
        $this->assertEquals($dto->price->getCurrentPrice(), $product->price->getCurrentPrice());
        $this->assertEquals($dto->price->getBasePrice(), $product->price->getBasePrice());
        $this->assertEquals($dto->status, $product->status);
    }

    #[Test]
    public function canUpdateProductDescription(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'description' => 'Old description',
        ]);

        $dto = new ProductUpdateDescriptionDto('New description');

        $this->productManager->updateDescription($product, $dto);

        $product->refresh();
        $this->assertEquals('New description', $product->description);
    }

    #[Test]
    public function canUpdateProductInstruction(): void
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'instruction' => 'Old instruction',
        ]);

        $dto = new ProductUpdateInstructionDto('New instruction');

        $this->productManager->updateInstruction($product, $dto);

        $product->refresh();
        $this->assertEquals('New instruction', $product->instruction);
    }

    #[Test]
    public function canAttachFeaturesToProduct(): void
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);

        // Создаем фичи в базе данных
        $feature1 = \App\Models\Feature::factory()->create(['id' => 1]);
        $feature2 = \App\Models\Feature::factory()->create(['id' => 2]);
        $feature3 = \App\Models\Feature::factory()->create(['id' => 3]);

        $features = [
            $feature1->id => 'Feature Value 1',
            $feature2->id => 'Feature Value 2',
            $feature3->id => '', // Пустое значение не должно прикрепиться
        ];

        $dto = new ProductAttachFeaturesDto($features);

        $this->productManager->attachFeatures($product, $dto);

        $product->refresh()->load('features');
        $this->assertCount(2, $product->features);
        $this->assertEquals('Feature Value 1', $product->features->find($feature1->id)->pivot->value);
        $this->assertEquals('Feature Value 2', $product->features->find($feature2->id)->pivot->value);
    }

    #[Test]
    public function canCreateStockItem(): void
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        $dto = new StockCreateDto('Stock item content');

        $stockItem = $this->productManager->createStockItem($product, $dto);

        $this->assertInstanceOf(StockItem::class, $stockItem);
        $this->assertEquals($product->id, $stockItem->product_id);
        $this->assertEquals('Stock item content', $stockItem->content);
        $this->assertEquals(StockItemStatus::AVAILABLE, $stockItem->status);
    }

    #[Test]
    public function canUpdateStockItem(): void
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        $stockItem = StockItem::factory()->create([
            'product_id' => $product->id,
            'content' => 'Old content',
        ]);

        $dto = new StockCreateDto('Updated content');

        $this->productManager->updateStockItem($stockItem, $dto);

        $stockItem->refresh();
        $this->assertEquals('Updated content', $stockItem->content);
    }

    #[Test]
    public function attachFeaturesRemovesExistingFeatures(): void
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);

        // Создаем фичи в базе данных
        $feature1 = \App\Models\Feature::factory()->create(['id' => 1]);
        $feature2 = \App\Models\Feature::factory()->create(['id' => 2]);
        $feature3 = \App\Models\Feature::factory()->create(['id' => 3]);

        // Сначала прикрепляем одни фичи
        $firstFeatures = [
            $feature1->id => 'First Value',
            $feature2->id => 'Second Value'
        ];
        $this->productManager->attachFeatures($product, new ProductAttachFeaturesDto($firstFeatures));

        $product->refresh()->load('features');
        $this->assertCount(2, $product->features);

        // Затем другие - старые должны удалиться
        $secondFeatures = [$feature3->id => 'Third Value'];
        $this->productManager->attachFeatures($product, new ProductAttachFeaturesDto($secondFeatures));

        $product->refresh()->load('features');
        $this->assertCount(1, $product->features);
        $this->assertEquals('Third Value', $product->features->first()->pivot->value);
        $this->assertEquals($feature3->id, $product->features->first()->id);
    }
}