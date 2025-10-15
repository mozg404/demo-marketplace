<?php

namespace App\Services\Demo;

use App\Data\Demo\DemoProductData;
use App\Enum\FeatureType;
use App\Enum\ProductStatus;
use App\Models\Product;
use App\Models\User;
use App\Services\Category\CategoryQuery;
use App\Services\Product\ProductCreator;
use App\Services\Product\ProductFeatureAttacher;
use App\Services\Product\ProductPreviewAttacher;
use App\Services\Product\Stock\StockCreator;
use App\Support\TextGenerator;
use App\ValueObjects\Price;
use Carbon\Carbon;

readonly class DemoProductCreator
{
    public function __construct(
        private CategoryQuery $categoryQuery,
        private ProductCreator $creator,
        private ProductPreviewAttacher $previewAttacher,
        private ProductFeatureAttacher $featureAttacher,
        private StockCreator $stockCreator,
    ) {
    }

    public function create(User $user, DemoProductData $data): Product
    {
        $category = $this->categoryQuery->getByFullPath($data->categoryFullPath);

        // Создание товара
        $product = $this->creator->create(
            user: $user,
            category: $category,
            name: $data->name,
            price: Price::random(),
            status: ProductStatus::ACTIVE,
            description: TextGenerator::paragraphs(include resource_path('data/demo_product_descriptions.php'), random_int(3, 7)),
            instruction: TextGenerator::paragraphs(include resource_path('data/demo_product_instructions.php'), random_int(1, 4)),
            createdAt: new Carbon(fake()->dateTimeBetween('-1 year'))
        );

        // Превью
        $this->previewAttacher->attachPreviewFromPath($product, $data->imagePath);

        // Динамические характеристики (Пока что захардкодим)
        $attachments = [];

        foreach ($category->features as $feature) {
            $attachments[$feature->id] = match($feature->type) {
                FeatureType::TEXT => fake()->word(),
                FeatureType::NUMBER => fake()->randomNumber(2),
                FeatureType::SELECT => fake()->randomElement(array_keys($feature->options)),
                FeatureType::CHECK => fake()->boolean(),
                default => 'DEFAULT',
            };
        }

        $this->featureAttacher->attachAllFromArray($product, $attachments);

        // Позиции на складе
        for ($i = 0; $i < config('demo.product_stock_count'); ++$i) {
            $this->stockCreator->create(
                product: $product,
                content: fake()->regexify('[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}')
            );
        }

        return $product;
    }
}