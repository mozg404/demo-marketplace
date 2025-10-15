<?php

namespace Database\Factories;

use App\Enum\FeatureType;
use App\Models\Feature;
use App\Models\Product;
use App\Models\ProductFeatureValue;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFeatureValueFactory extends Factory
{
    protected $model = ProductFeatureValue::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'feature_id' => Feature::factory(),
            'value' => fn (array $attributes) => $this->generateValueFor(Feature::find($attributes['feature_id']) ?? Feature::factory()->create()),
        ];
    }

    public function generateValueFor(Feature $feature): mixed
    {
        return match ($feature->type) {
            FeatureType::SELECT => $this->faker->randomElement(array_keys($feature->options)),
            FeatureType::TEXT => $this->faker->sentence,
        };
    }
}
