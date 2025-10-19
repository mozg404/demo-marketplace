<?php

namespace Database\Factories;

use App\Enum\StockItemStatus;
use App\Models\Product;
use App\Models\StockItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class StockItemFactory extends Factory
{
    protected $model = StockItem::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'status' => StockItemStatus::AVAILABLE,
            'content' => $this->faker->regexify('[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}'),
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StockItemStatus::AVAILABLE,
        ]);
    }

    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StockItemStatus::RESERVED,
        ]);
    }
}
