<?php

namespace Database\Factories;

use App\Enum\PaymentSource;
use App\Enum\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'external_id' => $this->faker->uuid(),
            'amount' => random_int(100, 1000),
            'status' => $this->faker->randomElement(PaymentStatus::cases())->value,
            'source' => $this->faker->randomElement(PaymentSource::cases())->value,
        ];
    }

    public function forReplenishment(): static
    {
        return $this->state([
            'source' => PaymentSource::REPLENISHMENT,
        ]);
    }

    public function forOrder(Order $order): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $order->user_id,
            'amount' => $order->amount,
            'source' => PaymentSource::ORDER,
            'sourceable_type' => $order::class,
            'sourceable_id' => $order->id,
        ]);
    }
}
