<?php

namespace App\Services\Demo;

use App\Data\Demo\DemoProductData;
use App\Support\TextGenerator;
use Illuminate\Support\Collection;

class DemoProductList
{
    public function raw(): array
    {
        return include resource_path('data/demo_products.php');
    }

    public function random(): DemoProductData
    {
        return $this->all()->random();
    }

    public function all(): Collection
    {
        return collect($this->toArray());
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->raw() as $rawData) {
            $data[] = $this->toData($rawData);
        }

        return $data;
    }

    public function toData(array $raw): DemoProductData
    {
        $name = $raw['name'];
        $imagePath = $raw['image'];

        if (is_array($name)) {
            $name = fake()->randomElement($name);
        }

        if (is_array($imagePath)) {
            $imagePath = fake()->randomElement($imagePath);
        }

        if (isset($raw['name_modifiers'])) {
            $name = TextGenerator::decoratedText($name, $raw['name_modifiers'], random_int(0, 2), fake()->randomElement(['. ', ', ', ' ']));
        }

        return new DemoProductData(
            name: $name,
            imagePath: $imagePath,
            categoryFullPath: $raw['category'],
        );
    }
}