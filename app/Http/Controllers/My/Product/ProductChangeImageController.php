<?php

namespace App\Http\Controllers\My\Product;

use App\DTO\Product\ProductAttachPreviewDto;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductManager;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class ProductChangeImageController extends Controller
{
    public function index(Product $product): Response
    {
        return Inertia::render('common/ImageUploaderModal', [
            'imageUrl' => $product->getFirstMediaUrl($product::MEDIA_COLLECTION_PREVIEW),
            'aspectRatio' => 3/4,
            'saveRoute' => route('my.products.change.image.update', $product->id),
            'product' => $product,
        ]);
    }

    public function update(
        Product $product,
        ProductAttachPreviewDto $dto,
        ProductManager $manager,
        Toaster $toaster
    ): RedirectResponse {
        try {
            $manager->attachPreview($product, $dto);
            $toaster->success('Изображение сохранено');

            return redirect()->back();
        } catch (FileCannotBeAdded $e) {
            $toaster->error('Не удалось загрузить изображение');

            return redirect()->back()->withErrors(['image' => 'Не удалось загрузить аватар']);
        }
    }
}
