<?php

namespace Database\Factories;

use App\Enum\ProductStatus;
use App\Models\Product;
use App\Models\User;
use App\Support\RatingCalculator;
use App\Support\TextGenerator;
use App\ValueObjects\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    private static array $names = [
        'Ключ Autodesk AutoCAD 2026',
        'Windows 11 Pro (Профессиональная)',
        'Windows 11 IoT Enterprise 2024 LTSC (Корпоративная)',
        'Windows 10 Enterprise 2021 LTSC',
        'Ключ активации Microsoft Windows 10 Pro, бессрочный (активация онлайн), виндовс 10 про бессрочный',
        'Ключ активации Windows 10 Pro x32/64, электронный, мультиязычный, бессрочный',
        'Выделенный ПРОКСИ Outline ключ НАВСЕГДА! Без скоростных ограничений по трафику',
        'ПРОКСИ VLESS для защищенного доступа в интернет НАВСЕГДА (без ограничения скорости)',
        'Подарочная карта Apple iTunes, Россия, 500 руб, электронный ключ',
        'Подарочная карта Apple iTunes, Россия, 600 руб, электронный ключ',
        'Microsoft Office 2019 Professional Plus LTSC / Электронный ключ активации / Бессрочная лицензия / Гарантия',
        'Microsoft Office 2021 Professional Plus Retail | Электронный ключ | Бессрочный | 1 ПК | Без привязки к учетной записи',
        'Microsoft Office 2024 Professional Plus LTSC Электронный ключ активации Бессрочная лицензия (без привязки к учетной записи)',
    ];

    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');
        $price = Price::random();

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->randomElement(static::$names),
            'current_price' => $price->getCurrentPrice(),
            'base_price' => $price->getBasePrice(),
            'status' => fake()->randomElement(ProductStatus::cases())->value,
            'description' => TextGenerator::paragraphs(include resource_path('data/demo_product_descriptions.php'), random_int(3, 7)),
            'instruction' => TextGenerator::paragraphs(include resource_path('data/demo_product_instructions.php'), random_int(1, 4)),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt),
        ];
    }

    public function isDraft(): self
    {
        return $this->state(fn (array $attributes) => ['status' => ProductStatus::DRAFT->value]);
    }

    public function isActive(): self
    {
        return $this->state(fn (array $attributes) => ['status' => ProductStatus::ACTIVE->value]);
    }

    public function isPaused(): self
    {
        return $this->state(fn (array $attributes) => ['status' => ProductStatus::PAUSED->value]);
    }

    public function withCalculatedRating(int $positiveCounter, int $negativeCounter): static
    {
        return $this->state(function (array $attributes) use ($positiveCounter, $negativeCounter) {
            return [
                'positive_feedbacks_count' => $positiveCounter,
                'negative_feedbacks_count' => $negativeCounter,
                'rating' => RatingCalculator::calculate($positiveCounter, $negativeCounter),
            ];
        });
    }

    public function withModifiedName(string|array $name, ?array $modifiers = null): self
    {
        return $this->state(function (array $attributes) use ($name, $modifiers) {
            $name = is_array($name) ? $this->faker->randomElement($name) : $name;

            return [
                'name' => TextGenerator::decoratedText($name, $modifiers ?? [], random_int(0, 2), $this->faker->randomElement(['. ', ', ', ' '])),
            ];
        });
    }

    public function withConcretePreview(string|array $path): self
    {
        return $this->afterCreating(function (Product $product) use ($path) {
            $path = is_array($path) ? $this->faker->randomElement($path) : $path;
            $product
                ->addMedia($path)
                ->preservingOriginal()
                ->toMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
        });
    }

    public function withPrice(Price $price): self
    {
        return $this->state(function (array $attributes) use ($price) {
            return [
                'current_price' => $price->getCurrentPrice(),
                'base_price' => $price->getBasePrice(),
            ];
        });
    }

}
