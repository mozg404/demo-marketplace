<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

class ProductPreviewAttacher
{
    public function attachPreviewFromUploadedFile(Product $product, UploadedFile $image): void
    {
        $product->clearMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
        $product->addMedia($image)->toMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
    }

    public function attachPreviewFromPath(Product $product, string $imagePath): void
    {
        $product->clearMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
        $product->addMedia($imagePath)
            ->preservingOriginal()
            ->toMediaCollection($product::MEDIA_COLLECTION_PREVIEW);
    }
}