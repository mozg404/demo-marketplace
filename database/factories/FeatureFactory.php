<?php

namespace Database\Factories;

use App\Enum\FeatureType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeatureFactory extends Factory
{
    public function definition(): array
    {
        $types = ['select'];
        $type = $this->faker->randomElement($types);

        return [
            'name' => $this->faker->unique()->word(),
            'type' => $type,
            'options' => $this->generateOptions($type),
        ];
    }

    public function withOptions(array $options): self
    {
        return $this->state(function (array $attributes) use ($options) {
            return [
                'options' => $options,
            ];
        });
    }

    public function withType(FeatureType $type): self
    {
        return $this->state(function (array $attributes) use ($type) {
            return [
                'type' => $type->value,
            ];
        });
    }

    protected function generateOptions(string $type): ?array
    {
        if (!in_array($type, ['select', 'multiselect'])) {
            return null;
        }

        $options = [];
        foreach ($this->faker->words(5) as $word) {
            $options[Str::slug($word)] = $word;
        }

        return $options;
    }
}
