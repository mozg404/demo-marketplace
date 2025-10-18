<?php

namespace App\Services\Product;

use App\DTO\Product\ProductAttachPreviewDto;
use App\DTO\Product\ProductBaseCreateDto;
use App\DTO\Product\ProductUpdateBaseDto;
use App\DTO\Product\ProductUpdateDescriptionDto;
use App\DTO\Product\ProductAttachFeaturesDto;
use App\DTO\Product\ProductUpdateInstructionDto;
use App\DTO\Product\StockCreateDto;
use App\Enum\ProductStatus;
use App\Enum\StockItemStatus;
use App\Models\Product;
use App\Models\StockItem;
use App\ValueObjects\Price;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

readonly class ProductManager
{
    public function createProduct(
        int $userId,
        int $categoryId,
        string $name,
        Price $price,
        ProductStatus $status = ProductStatus::DRAFT,
        ?string $description = null,
        ?string $instruction = null,
        ?Carbon $createdAt = null,
    ): Product {
        $product = new Product();
        $product->user_id = $userId;
        $product->category_id = $categoryId;
        $product->status = $status;
        $product->name = $name;
        $product->price = $price;
        $product->status = $status;

        if (isset($description)) {
            $product->description = $description;
        }

        if (isset($instruction)) {
            $product->instruction = $instruction;
        }

        if (isset($createdAt)) {
            $product->created_at = $createdAt;
        }

        $product->save();

        return $product;
    }

    public function createBaseProduct(int $userId, ProductBaseCreateDto $dto): Product
    {
        return $this->createProduct(
            $userId,
            $dto->category_id,
            $dto->name,
            $dto->price,
            ProductStatus::DRAFT,
        );
    }

    public function updateBaseProduct(Product $product, ProductUpdateBaseDto $dto): void
    {
        $product->category_id = $dto->category_id;
        $product->name = $dto->name;
        $product->price = $dto->price;
        $product->save();
    }

    public function updateDescription(Product $product, ProductUpdateDescriptionDto $dto): void
    {
        $product->description = $dto->description;
        $product->save();
    }

    public function updateInstruction(Product $product, ProductUpdateInstructionDto $dto): void
    {
        $product->instruction = $dto->instruction;
        $product->save();
    }

    public function attachFeatures(Product $product, ProductAttachFeaturesDto $dto): void
    {
        DB::transaction(static function () use ($product, $dto) {
            $product->features()->detach();

            if (!empty($dto->features)) {
                foreach ($dto->features as $id => $value) {
                    if (!empty($value)) {
                        $product->features()->attach($id, ['value' => $value]);
                    }
                }
            }
        });
    }

    public function attachPreview(Product $product, ProductAttachPreviewDto $dto): void
    {
        $product->clearMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
        $product->addMedia($dto->image)->toMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
    }

    public function attachPreviewFromPath(Product $product, string $imagePath): void
    {
        $product->clearMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
        $product->addMedia($imagePath)
            ->preservingOriginal()
            ->toMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
    }

    public function createStockItem(Product $product, StockCreateDto $dto): StockItem
    {
        $stockItem = new StockItem();
        $stockItem->product_id = $product->id;
        $stockItem->content = $dto->content;
        $stockItem->status = StockItemStatus::AVAILABLE;
        $stockItem->save();

        return $stockItem;
    }

    public function updateStockItem(StockItem $stockItem, StockCreateDto $dto): void
    {
        $stockItem->content = $dto->content;
        $stockItem->save();
    }
}