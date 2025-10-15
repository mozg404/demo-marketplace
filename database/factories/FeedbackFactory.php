<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'user_id' => User::factory(),
            'order_item_id' => OrderItem::factory(),
            'product_id' => Product::factory(),
            'seller_id' => User::factory(),
            'is_positive' => $this->faker->boolean(),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt),
        ];
    }

    public function withComment(): static
    {
        return $this->state(function (array $attributes) {
            $isPositive = $this->faker->boolean();

            return [
                'is_positive' => $isPositive,
                'comment' => $this->faker->randomElement(match ($isPositive) {
                    false => include resource_path('data/negative_feedback_comments.php'),
                    true => include resource_path('data/positive_feedback_comments.php'),
                }),
            ];
        });
    }

    public function isPositive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_positive' => true,
        ]);
    }

    public function isNegative(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_positive' => false,
        ]);
    }

    public function forOrderItem(OrderItem $orderItem): static
    {
        return $this->state(fn (array $attributes) => [
            'order_item_id' => $orderItem->id,
            'product_id' => $orderItem->product_id,
            'seller_id' => $orderItem->product->user_id,
        ]);
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
            'seller_id' => $product->user_id,
        ]);
    }

    public function forSeller(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'seller_id' => $user->id,
        ]);
    }
}
