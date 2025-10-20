<?php

namespace App\Services\Demo;

use App\Data\Demo\DemoProductData;
use App\DTO\Product\ProductAttachPreviewDto;
use App\DTO\Product\ProductAttachFeaturesDto;
use App\DTO\Product\StockCreateDto;
use App\Enum\FeatureType;
use App\Enum\ProductStatus;
use App\Models\Product;
use App\Models\User;
use App\Services\Category\CategoryQuery;
use App\Services\Product\ProductManager;
use App\Support\TextGenerator;
use App\ValueObjects\Price;
use Carbon\Carbon;

readonly class DemoProductCreator
{
    public function __construct(
        private CategoryQuery $categoryQuery,
        private ProductManager $productManager,
    ) {
    }

    public function create(User $user, DemoProductData $data): Product
    {
        $category = $this->categoryQuery->getByFullPath($data->categoryFullPath);

        // Создание товара
        $product = $this->productManager->createProduct(
            userId: $user->id,
            categoryId: $category->id,
            name: $data->name,
            price: Price::random(),
            status: ProductStatus::ACTIVE,
            description: TextGenerator::paragraphs(include resource_path('data/demo_product_descriptions.php'), random_int(3, 7)),
            instruction: TextGenerator::paragraphs(include resource_path('data/demo_product_instructions.php'), random_int(1, 4)),
            createdAt: new Carbon(fake()->dateTimeBetween('-1 year'))
        );

        // Превью
        $this->productManager->attachPreviewFromPath($product, $data->imagePath);

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

        $this->productManager->attachFeatures($product, ProductAttachFeaturesDto::from($attachments));

        for ($i = 0; $i < config('demo.product_stock_count'); ++$i) {
            $this->productManager->createStockItem(
                $product,
                new StockCreateDto(fake()->regexify('[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}'))
            );
        }

        return $product;
    }
}