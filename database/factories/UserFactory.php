<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\RatingCalculator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static string $password = '12345678';

    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year');

        return [
            'name' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make(static::$password),
            'remember_token' => Str::random(10),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt),
        ];
    }

    public function withCalculatedRating(int $positiveCounter, int $negativeCounter): static
    {
        return $this->state(function (array $attributes) use ($positiveCounter, $negativeCounter) {
            return [
                'positive_feedbacks_count' => $positiveCounter,
                'negative_feedbacks_count' => $negativeCounter,
                'seller_rating' => RatingCalculator::calculate($positiveCounter, $negativeCounter),
            ];
        });
    }

    public function withRandomAvatar(): Factory
    {
        $avatars = include resource_path('data/user_avatars.php');

        return $this->afterCreating(function (User $user) use ($avatars) {
            $user
                ->addMedia($this->faker->randomElement($avatars))
                ->preservingOriginal()
                ->toMediaCollection($user::MEDIA_COLLECTION_AVATAR);
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
