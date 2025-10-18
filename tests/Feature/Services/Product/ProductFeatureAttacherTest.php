<?php

namespace Tests\Feature\Services\Product;

use App\Models\Feature;
use App\Models\Product;
use App\Models\ProductFeatureValue;
use App\Services\Product\ProductFeatureAttacher;
use App\Services\Product\ProductManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFeatureAttacherTest extends TestCase
{
    use RefreshDatabase;

    private ProductFeatureAttacher $featureAttacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->featureAttacher = $this->app->make(ProductFeatureAttacher::class);
    }

    // -----------------------------------------------
    // changeFeatures(Product $product, array $features)
    // -----------------------------------------------

    public function testCorrectChangeFeatures(): void
    {
        $feature1 = Feature::factory()->create();
        $feature2 = Feature::factory()->create();
        $feature3 = Feature::factory()->create();
        $product = Product::factory()->create();
        ProductFeatureValue::factory()->for($feature1)->for($product)->create();
        $feature2Value = ProductFeatureValue::factory()->generateValueFor($feature2);
        $feature3Value = ProductFeatureValue::factory()->generateValueFor($feature3);

        $this->featureAttacher->attachAllFromArray($product, [
            $feature2->id => $feature2Value,
            $feature3->id => $feature3Value,
        ]);

        // Стерлась запись 1
        $this->assertDatabaseMissing(ProductFeatureValue::TABLE, [
            'product_id' => $product->id,
            'feature_id' => $feature1->id,
        ]);
        // Присутствует запись 2
        $this->assertDatabaseHas(ProductFeatureValue::TABLE, [
            'product_id' => $product->id,
            'feature_id' => $feature2->id,
            'value' => $feature2Value,
        ]);
        // Присутствует запись 3
        $this->assertDatabaseHas(ProductFeatureValue::TABLE, [
            'product_id' => $product->id,
            'feature_id' => $feature3->id,
            'value' => $feature3Value,
        ]);
    }
}
